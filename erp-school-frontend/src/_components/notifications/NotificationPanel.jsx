import React from "react";
import {
    Box,
    Typography,
    Divider,
    Button,
    Chip,
    CircularProgress,
    IconButton,
    Tooltip,
} from "@mui/material";
import {
    DoneAll as DoneAllIcon,
    Refresh as RefreshIcon,
    NotificationsNone as EmptyIcon,
} from "@mui/icons-material";
import { COLORS } from "../../_CSS/theme";
import { NotificationItem } from "./NotificationItem";

function NotificationsPanel({
    notifications,
    counts,
    loading,
    onDismiss,
    onDismissAll,
    onRefresh,
    onClose,
}) {
    return (
        <Box sx={{
            width: 380,
            maxWidth: '95vw',
            maxHeight: 500,
            display: 'flex',
            flexDirection: 'column',
            bgcolor: '#fff',
        }}>
            {/* En-tête */}
            <Box sx={{
                p: 2,
                borderBottom: `1px solid ${COLORS.borderLight}`,
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'space-between',
            }}>
                <Box>
                    <Typography variant="h6" sx={{ fontWeight: 700, color: COLORS.primary, fontSize: '1.1rem' }}>
                        Notifications
                    </Typography>
                    <Box sx={{ display: 'flex', gap: 0.5, mt: 0.5 }}>
                        {counts.error > 0 && (
                            <Chip
                                label={`${counts.error} critique${counts.error > 1 ? 's' : ''}`}
                                size="small"
                                sx={{ bgcolor: COLORS.dangerLight, color: COLORS.danger, fontWeight: 700, height: 20, fontSize: '0.7rem' }}
                            />
                        )}
                        {counts.warning > 0 && (
                            <Chip
                                label={`${counts.warning} alerte${counts.warning > 1 ? 's' : ''}`}
                                size="small"
                                sx={{ bgcolor: COLORS.warningLight, color: COLORS.warning, fontWeight: 700, height: 20, fontSize: '0.7rem' }}
                            />
                        )}
                    </Box>
                </Box>
                <Box sx={{ display: 'flex', gap: 0.5 }}>
                    <Tooltip title="Actualiser">
                        <IconButton size="small" onClick={onRefresh} sx={{ color: COLORS.primary }}>
                            <RefreshIcon fontSize="small" />
                        </IconButton>
                    </Tooltip>
                    {notifications.length > 0 && (
                        <Tooltip title="Tout marquer comme lu">
                            <IconButton size="small" onClick={onDismissAll} sx={{ color: COLORS.primary }}>
                                <DoneAllIcon fontSize="small" />
                            </IconButton>
                        </Tooltip>
                    )}
                </Box>
            </Box>

            {/* Liste */}
            <Box sx={{ flex: 1, overflowY: 'auto', p: 1 }}>
                {loading ? (
                    <Box sx={{ display: 'flex', justifyContent: 'center', py: 4 }}>
                        <CircularProgress size={32} sx={{ color: COLORS.primary }} />
                    </Box>
                ) : notifications.length === 0 ? (
                    <Box sx={{ textAlign: 'center', py: 5, px: 2 }}>
                        <EmptyIcon sx={{ fontSize: 56, color: COLORS.textMuted, mb: 1 }} />
                        <Typography variant="body2" sx={{ color: COLORS.textSecondary, fontWeight: 600 }}>
                            Aucune notification
                        </Typography>
                        <Typography variant="caption" sx={{ color: COLORS.textMuted }}>
                            Tout est à jour ! 🎉
                        </Typography>
                    </Box>
                ) : (
                    notifications.map((n) => (
                        <NotificationItem
                            key={n.id}
                            notification={n}
                            onDismiss={onDismiss}
                        />
                    ))
                )}
            </Box>

            {/* Pied */}
            {notifications.length > 0 && (
                <>
                    <Divider />
                    <Box sx={{ p: 1.5, textAlign: 'center' }}>
                        <Button
                            size="small"
                            onClick={onClose}
                            sx={{
                                color: COLORS.primary,
                                textTransform: 'uppercase',
                                fontWeight: 700,
                                fontSize: '0.8rem',
                            }}
                        >
                            Fermer
                        </Button>
                    </Box>
                </>
            )}
        </Box>
    );
}

export { NotificationsPanel };