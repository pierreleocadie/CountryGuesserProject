import { ReactNode } from "react";
import { IconButton, DialogTitle as DTitle } from "@mui/material";
import { AiFillCloseCircle } from 'react-icons/ai';

const DialogTitle = (props: DialogTitleProps) => {
    const { children, onClose, ...other } = props;
  
    return (
      <DTitle sx={{ m: 0, p: 2 }} {...other}>
        {children}
        {onClose ? (
          <IconButton
            onClick={onClose}
            sx={{
              position: 'absolute',
              right: 8,
              top: 8,
              color: (theme) => theme.palette.grey[500],
            }}
          >
            <AiFillCloseCircle />
          </IconButton>
        ) : null}
      </DTitle>
    );
}

interface DialogTitleProps {
    children?: ReactNode;
    onClose: () => void;
}

export default DialogTitle;