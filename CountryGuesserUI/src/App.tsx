import './App.css';
import {
  BrowserRouter as Router,
  Routes,
  Route,
} from "react-router-dom";
import { UserProvider } from './services/UserContext';
import { SnackbarProvider } from 'notistack';
import Login from './components/authentification/Login';
import Register from './components/authentification/Register';
import ForgetPassword from './components/authentification/ForgetPassword';
import Dashboard from './components/dashboard/Dashboard';
import Game from './components/game/Game';
import Statistics from './components/statistics/Statistics';
import About from './components/about/About';

function App() {
  return (
    <Router>
      <UserProvider>
        <SnackbarProvider maxSnack={1}>
          <Routes>
            <Route path="/" element={ <Dashboard /> } />
            <Route path="/login" element={ <Login /> } />
            <Route path="/register" element={ <Register /> } />
            <Route path="/forget" element={ <ForgetPassword /> } />
            <Route path="/game">
              <Route path="" element={ <Game /> } />
              <Route path="/game/:nbPlayers" element={ <Game /> } />
              <Route path="/game/:nbPlayers/:nbRounds" element={ <Game /> } />
            </Route>
            <Route path="/statistics" element={ <Statistics /> } />
            <Route path="/about" element={ <About /> } />
          </Routes>
        </SnackbarProvider>
      </UserProvider>
    </Router>
  );
}

export default App;
