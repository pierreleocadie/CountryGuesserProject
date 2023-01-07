import { ReactElement, forwardRef, Ref } from 'react';
import { Button, Dialog, DialogActions, DialogContent, DialogTitle, Slide } from "@mui/material";
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

const LoserDialog = (props: LoserDialogProps) => {
  return (
    <>
        <Dialog
        open={props.open}
        TransitionComponent={Transition}
        keepMounted
        aria-describedby="alert-dialog-slide-description"
        >
        <DialogContent sx={{ m: "auto" }}>
            <img src="/emoji.png" alt="Emoji" width="150" />
        </DialogContent>
        <DialogTitle>
            { !props.winnerName ?
              <span>Vous avez abandonné mais pas de panique !</span> :
              <span>Votre adsersaire <b>{ props.winnerName }</b> a gagné mais pas de panique !</span>
            }
            <br />
            Vous ferez mieux la prochaine fois.<br />
            La réponse était : <b>{ props.mysteryCountry.name }</b>
        </DialogTitle>
        <DialogActions>
            <Link style={{ textDecoration: 'none' }} to="/">
                <Button>Revenir à l'accueil</Button>
            </Link>
            <Link style={{ textDecoration: 'none' }} to="/game">
                <Button onClick={props.onReplay} variant="contained">Rejouer</Button>
            </Link>
        </DialogActions>
        </Dialog>
    </>
  );
}

interface LoserDialogProps {
    open: boolean;
    mysteryCountry: { name: string, flag: string, code: string, latLng: number[] };
    onReplay: () => void;
    winnerName: string;
}

export default LoserDialog;