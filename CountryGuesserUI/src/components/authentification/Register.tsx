import { useState, useEffect } from "react";
import { Grid, Paper, Avatar, TextField, Button, Typography } from "@mui/material";
import { Link, useNavigate } from "react-router-dom";
import { BsFillLockFill } from "react-icons/bs";
import { register } from "../../services/AuthService";
import './Authentification.styles.css';

const Register = () => {
    const navigate = useNavigate();

    const [nickname, setNickname] = useState("");
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [confirmPassword, setConfirmPassword] = useState("");

    const [response, setResponse] = useState<any>(null);

    // Changement de la couleur de fond
    useEffect(() => {document.body.style.backgroundColor = "#efeff0"}, []);

    useEffect(() => {
        if (response?.player_id)
            navigate('/');
   }, [response]);

    const handleSubmit = async (e: any) => {
        e.preventDefault();

        try {
            if (password === confirmPassword)
                setResponse(await register(nickname, email, password, confirmPassword));
            else
                setResponse(["Les mots de passe ne correspondent pas"]);
        } catch (err) {
            console.error("Erreur lors de l'enregistrement", err);
        }
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
                <Paper elevation={5} sx={{ borderRadius: 3 }} className="connectionPaper">
                    <Grid className="connectionGrid">
                        <Avatar sx={{ backgroundColor: '#1bbd7e', margin: 'auto' }}>
                            <BsFillLockFill />
                        </Avatar>
                        <h2>Créer mon compte</h2>
                        { !response?.id && response instanceof Array && <Typography color="red">{ response[0] }</Typography> }
                    </Grid>
                    <TextField value={nickname} onChange={(e) => setNickname(e.target.value)} name="username" sx={{ mt: 1, mb: 1 }} label="Nom d'utilisateur" placeholder="Entrez votre nom d'utilisateur" fullWidth required />
                    <TextField value={email} onChange={(e) => setEmail(e.target.value)} name="email" sx={{ mb: 1 }} label="Email" placeholder="Entrez votre email" fullWidth required />
                    <TextField value={password} onChange={(e) => setPassword(e.target.value)} name="password" sx={{ mb: 1 }} label="Mot de passe" placeholder="Entrez votre mot de passe" type="password" fullWidth required />
                    <TextField value={confirmPassword} onChange={(e) => setConfirmPassword(e.target.value)} name="confirmPassword" sx={{ mb: 1 }} label="Confirmer mot de passe" placeholder="Confirmez votre mot de passe" type="password" fullWidth required />
                    <Button type="submit" color="primary" variant="contained" className="connectionSubmit" fullWidth>Créer</Button>
                    <Typography>
                        Déjà inscrit ?&nbsp;<Link style={{ textDecoration: 'none' }} to="/login">Se connecter</Link>
                    </Typography>
                </Paper>
            </Grid>
        </Grid>
    </form>
  )
}

export default Register