import { ReactElement, useState, forwardRef, Ref } from 'react';
import { TextField, Button, Dialog, DialogActions, DialogContent, DialogTitle, Slide, Stack, Typography } from "@mui/material";
import { MdOutlineErrorOutline } from "react-icons/md";
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

const CustomGameDialog = (props: CustomGameDialogProps) => {
    const [nbPlayers, setNbPlayers] = useState(2);
    const [nbRounds, setNbRounds] = useState(7);

  return (
    <>
        <Dialog
        open={props.open}
        TransitionComponent={Transition}
        keepMounted
        aria-describedby="alert-dialog-slide-description"
        onClose={props.onClose}
        >
        <DialogTitle>
            Partie personnalisée
        </DialogTitle>
        <DialogContent>
          <Stack mt={2} justifyContent="center" alignItems="center" gap={3}>
            <TextField label="Nombre de joueurs" value={nbPlayers} onChange={(e: any) => setNbPlayers(e.target.value)} />
            <TextField label="Nombre de tours" value={nbRounds} onChange={(e: any) => setNbRounds(e.target.value)} />
            <Stack direction="column" justifyContent="center" alignItems="center" gap={1}>
              <MdOutlineErrorOutline fontSize={15} />
              <Typography textAlign="center" fontStyle="italic">
                Le nombre de tours de jeu doit permettre de déterminer un gagnant.<br />
                Par exemple, vous pouvez faire une partie avec 2 joueurs et 7 tours
                mais pas une partie avec 2 joueurs et 8 tours car il y a un risque d'égalité.
              </Typography>
            </Stack>
          </Stack>
        </DialogContent>
        <DialogActions>
            <Link style={{ textDecoration: 'none', margin: "auto" }} to={`/game/${nbPlayers}/${nbRounds}`}>
                <Button variant="contained" disabled={nbPlayers <= 1 || nbRounds % nbPlayers === 0}>Lancer la partie</Button>
            </Link>
        </DialogActions>
        </Dialog>
    </>
  );
}

interface CustomGameDialogProps {
    open: boolean;
    onClose: () => void;
}

export default CustomGameDialog;