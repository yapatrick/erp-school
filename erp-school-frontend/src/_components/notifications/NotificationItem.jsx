import React from "react";
import { useNavigate } from "react-router-dom";
import { Box, Typography, IconButton, Chip } from "@mui/material";
import {
    Error as ErrorIcon,
    Warning as WarningIcon,
    Info as InfoIcon,
    Close as CloseIcon,
    ChevronRight as ChevronRightIcon,
} from "@mui/icons-material";
import { COLORS } from "../../_CSS/theme";

const SEVERITY_STYLES = {
    error: { color: COLORS.danger, bg: COLORS.dangerLight, icon: <ErrorIcon /> },
    warning: { color: COLORS.warning, bg: COLORS.warningLight, icon: <WarningIcon /> },
    info: { color: COLORS.accent, bg: COLORS.accentLight, icon: <InfoIcon /> },
};

function NotificationItem({ notification, onDismiss }) {
    const navigate = useNavigate();
    const style = SEVERITY_STYLES[notification.severity] || SEVERITY_STYLES.info;

    const handleClick = () => {
        if (notification.link) {
            navigate(notification.link);
            onDismiss?.(notification.id);
        }
    };

    return (
        <Box
            onClick={handleClick}
            sx={{
                display: 'flex',
                alignItems: 'flex-start',
                gap: 1.5,
                p: 1.5,
                borderRadius: 2,
                cursor: notification.link ? 'pointer' : 'default',
                transition: 'background-color 0.2s',
                '&:hover': { bgcolor: '#f8f9fa' },
                position: 'relative',
            }}
        >
            <Box sx={{
                width: 36,
                height: 36,
                borderRadius: '10px',
                bgcolor: style.bg,
                color: style.color,
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                flexShrink: 0,
            }}>
                {style.icon}
            </Box>

            <Box sx={{ flex: 1, minWidth: 0 }}>
                <Typography variant="body2" sx={{ fontWeight: 700, color: COLORS.textPrimary, mb: 0.3 }}>
                    {notification.title}
                </Typography>
                <Typography variant="caption" sx={{ color: COLORS.textSecondary, display: 'block', lineHeight: 1.4 }}>
                    {notification.message}
                </Typography>
            </Box>

            <IconButton
                size="small"
                onClick={(e) => {
                    e.stopPropagation();
                    onDismiss?.(notification.id);
                }}
                sx={{
                    color: COLORS.textMuted,
                    '&:hover': { bgcolor: 'rgba(0,0,0,0.05)' },
                }}
            >
                <CloseIcon fontSize="small" />
            </IconButton>
        </Box>
    );
}

export { NotificationItem };