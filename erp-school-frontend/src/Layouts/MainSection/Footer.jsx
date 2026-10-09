import React from "react";
import { Box, Typography, Divider } from "@mui/material";
import { COLORS } from "../../_CSS/theme";

function Footer() {
  const currentYear = new Date().getFullYear();

    return (
        <Box
            sx={{
                mt: 4,
                pt: 2,
            }}
        >
            <Divider sx={{ mb: 2, borderColor: COLORS.borderLight }} />
            <Box sx={{ textAlign: "center" }}>
                <Typography
                    variant="caption"
                    sx={{
                        color: COLORS.textMuted,
                        fontSize: "0.8rem",
                        letterSpacing: "0.3px",
                    }}
                >
                    <strong style={{ color: COLORS.primary, fontWeight: 500 }}>ERP SCHOOL</strong>
                    {" · "}
                    Tous droits réservés
                    {" · "}
                    {currentYear}
                </Typography>
            </Box>
        </Box>
    );
}

export  { Footer }