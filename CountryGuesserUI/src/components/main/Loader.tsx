import { Box, CircularProgress } from '@mui/material';

const Loader = () => {
  return (
    <Box sx={{ display: 'flex', justifyContent: "center", alignItems: "center", width: "100%" }}>
      <CircularProgress />
    </Box>
  );
}

export default Loader;