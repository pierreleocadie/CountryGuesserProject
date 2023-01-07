import { useState, useContext, MouseEvent, useEffect } from "react";
import { AppBar, Box, Toolbar, MenuItem, Button, Container, Menu, Typography, IconButton } from "@mui/material";
import { Link } from "react-router-dom";
import { AiOutlineMenu } from "react-icons/ai";
import { isAuthenticated } from "../../services/AuthService";
import UserContext from "../../services/UserContext";
import ProfileDialog from "../user/ProfileDialog";
import ParametersDialog from "../user/ParametersDialog";

const pages = [
    {
        name: 'Accueil',
        link: '/'
    },
    {
        name: 'Statistiques',
        link: '/statistics'
    },
    {
        name: 'À propos',
        link: '/about'
    }];

function Navbar() {
    const [anchorElNav, setAnchorElNav] = useState<null | HTMLElement>(null);
    const [anchorElUser, setAnchorElUser] = useState<null | HTMLElement>(null);

    const [showProfile, setShowProfile] = useState(false);
    const [showParameters, setShowParameters] = useState(false);

    const [currentUser, setCurrentUser] = useContext(UserContext);

    useEffect(() => {
      setCurrentUser(isAuthenticated());
    }, []);
  
    const handleOpenNavMenu = (event: MouseEvent<HTMLElement>) => {
      setAnchorElNav(event.currentTarget);
    };
    const handleOpenUserMenu = (event: MouseEvent<HTMLElement>) => {
      setAnchorElUser(event.currentTarget);
    };
  
    const handleCloseNavMenu = () => {
      setAnchorElNav(null);
    };
  
    const handleCloseUserMenu = () => {
      setAnchorElUser(null);
    };

    const handleCloseProfileDialog = () => {
      setAnchorElUser(null);
      setShowProfile(false);
    }

    const handleCloseParametersDialog = () => {
      setAnchorElUser(null);
      setShowParameters(false);
    }

    const handleLogOut = () => {
      setAnchorElUser(null);
      localStorage.removeItem('user');
      setCurrentUser({ player_id: null, nickname: "", email: "", credential: "" });
    }

    const settings = [
      {
          name: 'Profil',
          action: () => setShowProfile(true),
      },
      {
          name: 'Paramètres',
          action: () => setShowParameters(true),
      },
      {
          name: 'Déconnexion',
          action: handleLogOut,
      }
    ];
  
    return (
      <>
        <AppBar position="static" sx={{ backgroundColor: "transparent" }}>
          <Container maxWidth="xl">
            <Toolbar disableGutters>
              <Typography
                variant="h6"
                noWrap
                component={Link}
                to="/"
                sx={{
                  mr: 5,
                  display: { xs: 'none', md: 'flex' },
                }}
              >
                <img src="logo.png" alt="CountryGuesser Logo" width={150} />
              </Typography>
    
              <Box sx={{ flexGrow: 1, display: { xs: 'flex', md: 'none' } }}>
                <IconButton
                  size="large"
                  aria-label="account of current user"
                  aria-controls="menu-appbar"
                  aria-haspopup="true"
                  onClick={handleOpenNavMenu}
                  color="inherit"
                >
                  <AiOutlineMenu />
                </IconButton>
                <Menu
                  id="menu-appbar"
                  anchorEl={anchorElNav}
                  anchorOrigin={{
                    vertical: 'bottom',
                    horizontal: 'left',
                  }}
                  keepMounted
                  transformOrigin={{
                    vertical: 'top',
                    horizontal: 'left',
                  }}
                  open={Boolean(anchorElNav)}
                  onClose={handleCloseNavMenu}
                  sx={{
                    display: { xs: 'block', md: 'none' },
                  }}
                >
                  {pages.map((page, i) => (
                    <Link style={{ textDecoration: 'none' }} key={i} to={page.link}>
                      <MenuItem onClick={handleCloseNavMenu}>
                          <Typography textAlign="center" color="black">
                              {page.name}
                          </Typography>
                      </MenuItem>
                    </Link>
                  ))}
                </Menu>
              </Box>
              <Typography
                variant="h5"
                noWrap
                component={Link}
                to="/"
                sx={{
                  mr: 2,
                  display: { xs: 'flex', md: 'none' },
                  flexGrow: 1,
                }}
              >
                <img src="logo.png" alt="CountryGuesser Logo" width={150} />
              </Typography>
              <Box sx={{ flexGrow: 1, display: { xs: 'none', md: 'flex' } }}>
                {pages.map((page, i) => (
                  <Link style={{ textDecoration: 'none' }} key={i} to={page.link}>
                      <Button
                      onClick={handleCloseNavMenu}
                      sx={{ my: 2, color: 'white', display: 'block' }}
                      >
                      {page.name}
                      </Button>
                  </Link>
                ))}
              </Box>
    
              <Box sx={{ flexGrow: 0 }}>
                <IconButton onClick={handleOpenUserMenu} sx={{ p: 0 }}>
                  { currentUser?.player_id ?
                    <>
                      <ProfileDialog
                      currentUser={currentUser}
                      open={showProfile}
                      handleClose={handleCloseProfileDialog} />
                      <ParametersDialog
                      open={showParameters}
                      handleClose={handleCloseParametersDialog} />

                      <Typography
                      sx={{
                        backgroundColor: "lightgray",
                        borderRadius: 50,
                        fontSize: "1.2rem",
                        width: 25,
                        height: 25,
                        p: 1,
                        pt: 1,
                        color: "white" }}>
                        { currentUser.nickname.substring(0, 1).toUpperCase() }
                      </Typography>
                    </>
                  :
                  <Link to="/login" style={{ textDecoration: "none" }}>
                    <Button sx={{ color: "lightgray", borderColor: "gray", '&:hover': { borderColor: "white", color: "white" } }} variant="outlined">
                      Connexion
                    </Button>
                  </Link>
                  }                  
                </IconButton>
                <Menu
                  sx={{ mt: '45px' }}
                  id="menu-appbar"
                  anchorEl={anchorElUser}
                  anchorOrigin={{
                    vertical: 'top',
                    horizontal: 'right',
                  }}
                  keepMounted
                  transformOrigin={{
                    vertical: 'top',
                    horizontal: 'right',
                  }}
                  open={Boolean(anchorElUser)}
                  onClose={handleCloseUserMenu}
                >
                  {settings.map((setting, i) => (
                    <MenuItem autoFocus={false} key={i} onClick={setting.action}>
                      <Typography textAlign="center">{setting.name}</Typography>
                    </MenuItem>
                  ))}
                </Menu>
              </Box>
            </Toolbar>
          </Container>
        </AppBar>
      </>
    );
  }
  export default Navbar;