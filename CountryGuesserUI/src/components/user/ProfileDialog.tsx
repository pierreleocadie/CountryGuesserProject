import { Typography, Dialog, DialogContent, Stack } from "@mui/material";
import { useEffect, useState } from "react";
import { GiTrophyCup } from "react-icons/gi";
import { FaGamepad } from "react-icons/fa";
import { RiEyeCloseFill } from "react-icons/ri";
import { getPlayerStatistics } from "../../utils/statistics.utils";
import DialogTitle from "./DialogTitle";

const ProfileDialog = (props: ProfileDialogProps) => {
  const [wonGames, setWonGames] = useState(0);
  const [losedGames, setLosedGames] = useState(0);
  const [playedGames, setPlayedGames] = useState(0);

  useEffect(() => {
    // Récupération des statistiques du joueur
    getPlayerStatistics(props.currentUser).then(data => {
      setWonGames(data.wonGames);
      setPlayedGames(data.playedGames);
      setLosedGames(data.losedGames); 
    });
  }, [props.currentUser]);

  return (
      <Dialog
        sx={{ p: 2 }}
        onClose={props.handleClose}
        open={props.open}
        fullWidth
        maxWidth="sm"
      >
        <DialogTitle onClose={props.handleClose}>
          Profil
        </DialogTitle>
        <DialogContent dividers>
          <Stack justifyContent="center" alignItems="center" gap={5}>
            <Stack direction="column" justifyContent="space-around" alignItems="center">
              <Stack
              justifyContent="center"
              alignItems="center"
              sx={{
                backgroundColor: "lightgray",
                borderRadius: 50,
                fontSize: "1.2rem",
                width: 25,
                height: 25,
                p: 1,
                pt: 1,
                color: "white" }}>
                  { props.currentUser.nickname.substring(0, 1).toUpperCase() }
              </Stack>
              <Typography fontWeight="bold" fontFamily={"Oswald, sans-serif"} fontSize={20}>
                { props.currentUser.nickname }
              </Typography>
            </Stack>
            <Stack direction="column" justifyContent="center">
              <Stack direction="row" alignItems="center" gap={2}>
                <GiTrophyCup color="yellow" fontSize={25} />
                <Typography>{ wonGames } parties gagnées</Typography>
              </Stack>

              <Stack direction="row" alignItems="center" gap={2}>
                <RiEyeCloseFill color="red" fontSize={25} />
                <Typography>{ losedGames } parties perdues</Typography>
              </Stack>

              <Stack direction="row" alignItems="center" gap={2}>
                <FaGamepad fontSize={25} />
                <Typography>{ playedGames } parties jouées</Typography>
              </Stack>
            </Stack>
          </Stack>
        </DialogContent>
      </Dialog>
  );
}

interface ProfileDialogProps {
  handleClose: () => void;
  open: boolean;
  currentUser: any;
}

export default ProfileDialog;