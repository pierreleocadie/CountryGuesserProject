import { ReactElement, forwardRef, Ref } from 'react';
import { Box, Button, Dialog, DialogActions, DialogContent, DialogTitle, Slide, Typography } from "@mui/material";
import { TransitionProps } from '@mui/material/transitions';
import { Link } from "react-router-dom";

const Transition = forwardRef(function Transition(
  props: TransitionProps & {
    children: ReactElement<any, any>;
  },
  ref: Ref<unknown>,
) {
  return <Slide direction="up" ref={ref} {...props} />;
});

const ErrorDialog = (props: ErrorDialogProps) => {
  return (
    <>
        <Dialog
        open={props.open}
        TransitionComponent={Transition}
        keepMounted
        aria-describedby="alert-dialog-slide-description"
        >
        <DialogContent sx={{ m: "auto" }}>
            <img src="/emoji-crying.png" alt="Émoji en pleurs" width="150" />
        </DialogContent>
        <DialogTitle>
            <Typography fontWeight="bold" textAlign="center">Mince !</Typography>
            <Typography textAlign="center">
              Votre adversaire a quitté la partie,<br />
              il avait probablement trop peur de perdre !<br /><br />
              Relancez une partie !
            </Typography>
        </DialogTitle>
        <DialogActions>
            <Link style={{ textDecoration: 'none' }} to="/">
                <Button>Revenir à l'accueil</Button>
            </Link>
            <Box style={{ textDecoration: 'none' }}>
                <Button onClick={props.onReplay}  variant="contained">Rejouer</Button>
            </Box>
        </DialogActions>
        </Dialog>
    </>
  );
}

interface ErrorDialogProps {
    open: boolean;
    onReplay: () => void;
}

export default ErrorDialog;