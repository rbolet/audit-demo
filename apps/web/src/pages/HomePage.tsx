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
        <Typography variant="h5" sx={{ mb: 2, fontWeight: 'bold', color: 'red' }}>
          DON'T PANIC
        </Typography>
      </Paper>
    </Box>
  );
}
