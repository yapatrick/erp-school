// src/_hooks/useNotifications.js
import { useEffect, useState, useCallback, useRef } from "react";
import axios from "axios";
import { authHeader } from "../_helpers/auth-header";
import { API_URL } from "../_api/ApiUrl";

const STORAGE_KEY = "mitukas_dismissed_notifications";
const REFRESH_INTERVAL = 5 * 60 * 1000; // 5 minutes

export const useNotifications = () => {
    const [notifications, setNotifications] = useState([]);
    const [counts, setCounts] = useState({ total: 0, error: 0, warning: 0, info: 0 });
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState("");
    const [dismissedIds, setDismissedIds] = useState(() => {
        try {
            const stored = localStorage.getItem(STORAGE_KEY);
            return stored ? JSON.parse(stored) : [];
        } catch {
            return [];
        }
    });

    const intervalRef = useRef(null);

    // -------- Chargement --------

    const fetchNotifications = useCallback(async () => {
        try {
            setLoading(true);
            setError("");

            const response = await axios.get(`${API_URL}notifications`, {
                headers: { ...authHeader(), Accept: "application/json" },
            });

            const data = response.data?.notifications ?? [];
            const serverCounts = response.data?.counts ?? { total: 0, error: 0, warning: 0, info: 0 };

            setNotifications(data);
            setCounts(serverCounts);
        } catch (err) {
            setError("Impossible de charger les notifications.");
            setNotifications([]);
            setCounts({ total: 0, error: 0, warning: 0, info: 0 });
        } finally {
            setLoading(false);
        }
    }, []);

    // -------- Cycle de vie --------

    useEffect(() => {
        fetchNotifications();

        intervalRef.current = setInterval(fetchNotifications, REFRESH_INTERVAL);
        return () => {
            if (intervalRef.current) clearInterval(intervalRef.current);
        };
    }, [fetchNotifications]);

    // -------- Gestion des dismiss --------

    const dismiss = useCallback((id) => {
        setDismissedIds((prev) => {
            const next = [...new Set([...prev, id])];
            try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(next));
            } catch { /* ignore */ }
            return next;
        });
    }, []);

    const dismissAll = useCallback(() => {
        const allIds = notifications.map((n) => n.id);
        setDismissedIds(allIds);
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(allIds));
        } catch { /* ignore */ }
    }, [notifications]);

    const resetDismissed = useCallback(() => {
        setDismissedIds([]);
        try {
            localStorage.removeItem(STORAGE_KEY);
        } catch { /* ignore */ }
    }, []);

    // -------- Notifications actives (non masquées) --------

    const activeNotifications = notifications.filter((n) => !dismissedIds.includes(n.id));

    // Recalcul des counts sur les notifications actives
    const activeCounts = {
        total: activeNotifications.length,
        error: activeNotifications.filter((n) => n.severity === "error").length,
        warning: activeNotifications.filter((n) => n.severity === "warning").length,
        info: activeNotifications.filter((n) => n.severity === "info").length,
    };

    return {
        notifications: activeNotifications,
        allNotifications: notifications,
        counts: activeCounts,
        loading,
        error,
        refresh: fetchNotifications,
        dismiss,
        dismissAll,
        resetDismissed,
    };
};