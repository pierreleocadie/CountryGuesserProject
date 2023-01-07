import { Stack, Typography, Dialog, DialogContent } from "@mui/material";
import { MdOutlineErrorOutline } from "react-icons/md";
import DialogTitle from "./DialogTitle";

const ParametersDialog = (props: ParametersDialogProps) => {
  return (
      <Dialog
        sx={{ p: 2 }}
        onClose={props.handleClose}
        open={props.open}
        fullWidth
        maxWidth="sm"
      >
        <DialogTitle onClose={props.handleClose}>
          Paramètres
        </DialogTitle>
        <DialogContent dividers>
          <Stack direction="row" justifyContent="center" alignItems="center" gap={1}>
            <MdOutlineErrorOutline />
            <Typography fontStyle="italic">
              Cette fonctionnalité n'est pas encore disponible.<br />
              Reviens une autre fois !
            </Typography>
          </Stack>
        </DialogContent>
      </Dialog>
  );
}

interface ParametersDialogProps {
  handleClose: () => void;
  open: boolean;
}

export default ParametersDialog;