import { useState, useEffect } from "react";
import { Grid, Paper, Avatar, TextField, Button, Typography } from "@mui/material";
import { Link, useNavigate } from "react-router-dom";
import { BsFillLockFill } from "react-icons/bs";
import { login } from "../../services/AuthService";
import './Authentification.styles.css';

const Login = () => {
    const navigate = useNavigate();

    const [nicknameEmail, setNicknameEmail] = useState("");
    const [password, setPassword] = useState("");

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
            setResponse(await login(nicknameEmail, password));
        } catch (err) {
            console.error("Erreur lors de la connexion", err);
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
                <Paper elevation={10} className="connectionPaper">
                    <Grid className="connectionGrid">
                        <Avatar sx={{ backgroundColor: '#1bbd7e', margin: 'auto' }}>
                            <BsFillLockFill />
                        </Avatar>
                        <h2>Se connecter</h2>
                        { !response?.id && response instanceof Array && <Typography color="red">{ response[0] }</Typography> }
                    </Grid>
                    <TextField value={nicknameEmail} onChange={(e): void => setNicknameEmail(e.target.value)} name="nickname_email" sx={{ mt: 1, mb: 1 }} label="Nom d'utilisateur / Email" placeholder="Entrez votre nom d'utilisateur ou e-mail" fullWidth required />
                    <TextField value={password} onChange={(e): void => setPassword(e.target.value)} name="password" sx={{ mb: 1 }} label="Mot de passe" placeholder="Entrez votre mot de passe" type="password" fullWidth required />

                    <Button type="submit" color="primary" variant="contained" className="connectionSubmit" fullWidth>Se connecter</Button>
                    <Typography sx={{ textAlign: "center" }}>
                        <Link style={{ textDecoration: 'none' }} to="/forget">Mot de passe oublié ?</Link>
                    </Typography>
                    <Typography>
                        Pas encore de compte ?&nbsp;<Link style={{ textDecoration: 'none' }} to="/register">S'enregistrer</Link>
                    </Typography>
                </Paper>
            </Grid>
        </Grid>
    </form>
  )
}

export default Login;