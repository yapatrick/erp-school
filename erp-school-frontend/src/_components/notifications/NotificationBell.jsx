import React, { useState } from "react";
import {
    IconButton,
    Badge,
    Popover,
    Tooltip,
} from "@mui/material";
import {
    Notifications as NotificationsIcon,
} from "@mui/icons-material";
import { COLORS } from "../../_CSS/theme";
import { useNotifications } from "../../_hooks/useNotifications";
import { NotificationsPanel } from "./NotificationsPanel";

function NotificationsBell() {
    const [anchorEl, setAnchorEl] = useState(null);
    const {
        notifications,
        counts,
        loading,
        refresh,
        dismiss,
        dismissAll,
    } = useNotifications();

    const open = Boolean(anchorEl);

    const handleClick = (event) => {
        setAnchorEl(event.currentTarget);
    };

    const handleClose = () => {
        setAnchorEl(null);
    };

    return (
        <>
            <Tooltip title="Notifications">
                <IconButton
                    onClick={handleClick}
                    sx={{
                        color: '#fff',
                        '&:hover': { bgcolor: 'rgba(255,255,255,0.15)' },
                    }}
                >
                    <Badge
                        badgeContent={counts.total}
                        color="error"
                        overlap="circular"
                        sx={{
                            '& .MuiBadge-badge': {
                                fontWeight: 700,
                                fontSize: '0.7rem',
                                minWidth: 18,
                                height: 18,
                            },
                        }}
                    >
                        <NotificationsIcon />
                    </Badge>
                </IconButton>
            </Tooltip>

            <Popover
                open={open}
                anchorEl={anchorEl}
                onClose={handleClose}
                anchorOrigin={{ vertical: 'bottom', horizontal: 'right' }}
                transformOrigin={{ vertical: 'top', horizontal: 'right' }}
                PaperProps={{
                    sx: {
                        mt: 1,
                        borderRadius: 2,
                        boxShadow: '0 8px 32px rgba(0,0,0,0.12)',
                        border: `1px solid ${COLORS.borderLight}`,
                        overflow: 'hidden',
                    },
                }}
            >
                <NotificationsPanel
                    notifications={notifications}
                    counts={counts}
                    loading={loading}
                    onDismiss={dismiss}
                    onDismissAll={dismissAll}
                    onRefresh={refresh}
                    onClose={handleClose}
                />
            </Popover>
        </>
    );
}

export { NotificationsBell };