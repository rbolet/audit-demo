import Typography from '@mui/material/Typography';
import Box from '@mui/material/Box';
import Paper from '@mui/material/Paper';

export default function HomePage() {
  return (
    <Box>
      <Typography variant="h3" component="h1" gutterBottom>
        Welcome to Audit Demo
      </Typography>
      <Paper sx={{ p: 3, mt: 3 }}>
        <Typography variant="h5" gutterBottom>
          Getting Started
        </Typography>
        <Typography variant="body1" paragraph>
          This is a modern full-stack application for managing physical condition
          audits at various locations.
        </Typography>
        <Typography variant="body2" color="text.secondary">
          Built with React, TypeScript, MaterialUI, and Laravel
        </Typography>
      </Paper>
    </Box>
  );
}
