import { Box, LinearProgress } from '@mui/material';

const LoadingBar = (props: LoadingBarProps) => {
  return (
    <Box sx={{  width: '100%', display: props.visible ? "block" : "none" }}>
      <LinearProgress />
    </Box>
  );
}

interface LoadingBarProps {
    visible: boolean;
}

export default LoadingBar;