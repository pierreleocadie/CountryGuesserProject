import { useState, useEffect } from "react";
import { Grid, Paper, Avatar, TextField, Button, Typography } from "@mui/material";
import { Link } from "react-router-dom";
import { MdLockClock } from "react-icons/md";
import './Authentification.styles.css';

const ForgetPassword = () => {
    const [nicknameEmail, setNicknameEmail] = useState("");

    // Changement de la couleur de fond
    useEffect(() => {document.body.style.backgroundColor = "#efeff0"}, []);

    const handleSubmit = async (e: any) => {
        // À faire
    }

  return (
    <form onSubmit={handleSubmit}>
        <Grid container
        sx={{ height: "100vh", width: "70%", m: "auto" }}
        justifyContent="space-around"
        alignItems="center">
            <Grid item>
                <img src="logo-light.png" alt="Country Guesser Logo" width={500} />
            </Grid>
            
            <Grid item>
                <Paper elevation={10} className="connectionPaper">
                    <Grid className="connectionGrid">
                        <Avatar sx={{ backgroundColor: '#1bbd7e', margin: 'auto' }}>
                            <MdLockClock />
                        </Avatar>
                        <h2>Mot de passe oublié ?</h2>
                        <Typography
                        color="gray"
                        fontSize="0.8rem"
                        mb={3}>
                            Entrez votre nom d'utilisateur ou email.<br />
                            Si un compte correspond, vous recevrez un lien pour réinitialiser votre mot de passe.
                        </Typography>
                    </Grid>
                    <TextField value={nicknameEmail} onChange={(e): void => setNicknameEmail(e.target.value)} name="nickname_email" sx={{ mt: 1, mb: 1 }} label="Nom d'utilisateur / Email" placeholder="Entrez votre nom d'utilisateur ou e-mail" fullWidth required />

                    <Button type="submit" color="primary" variant="contained" className="connectionSubmit" fullWidth>Envoyer le lien</Button>
                    <Typography align="center">
                        Problème réglé ?&nbsp;<Link style={{ textDecoration: 'none' }} to="/login">Se connecter</Link>
                    </Typography>
                </Paper>
            </Grid>
        </Grid>
    </form>
  )
}

export default ForgetPassword;