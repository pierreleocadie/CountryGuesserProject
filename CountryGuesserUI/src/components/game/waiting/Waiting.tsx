import { useEffect, useState, ReactNode } from "react";
import { Stack, Box, Typography, Skeleton, List, ListItem, ListItemButton, ListItemText, ListItemIcon, Zoom, Alert, Slide } from "@mui/material";
import { Question } from "../../../utils/interfaces/Question.interface";
import { getRandomQuestion, validateAnswer } from "../../../utils/questions.utils";
import "../../../animations/earth.animation.css";
import "../../../animations/bounce.animation.css";
import "./Waiting.styles.css";

const Waiting = (props: WaitingProps) => {
    const [quote, setQuote] = useState({
        content: "",
        author: "",
    });
    const [question, setQuestion] = useState<Question | null>({
        question: "",
        answers: [],
        answer: "",
    });
    const [shake, setShake] = useState(false);
    const [canPlay, setCanPlay] = useState(true);

    // Changement de la couleur de fond
    useEffect(() => {
        document.body.style.backgroundColor = "#efeff0";

        // Chargement d'une question du quiz
        setQuestion(getRandomQuestion());

        // Chargement d'une citation
        fetchQuote()
        .then(q => q.text())
        .then(q => {
            const data = q.split("-");
            setQuote({
                content: data[0],
                author: data[1] !== "null" ? data[1] : "Inconnu",
            });
        });
    }, []);

    const fetchQuote = async () => {
        const options = {
            method: 'POST',
            headers: {
                'X-RapidAPI-Key': 'cfad58c9b7mshf6ef6bc9f8f936ap150904jsnca123ed13b0a',
                'X-RapidAPI-Host': 'motivational-quotes1.p.rapidapi.com'
            },
            body: '{"key1":"value","key2":"value"}'
        };
        
        return await fetch('https://motivational-quotes1.p.rapidapi.com/motivation', options);
    }

    const handleAnswer = (proposition: string) => {
        if (!question) return;

        if (validateAnswer(proposition, question.answer)) {
            const randomQuestion = getRandomQuestion();

            if (randomQuestion) setQuestion(randomQuestion);
            else setCanPlay(false); // S'il n'y a plus de questions, on arrête le jeu
        } else {
            setShake(true);
        }
    }

    return !props.launchFoundPlayersAnimation ? (
        <Stack
        direction="row"
        justifyContent="space-around"
        alignItems="center"
        flexWrap="wrap"
        height="100vh"
        mx={10}>
            <Stack
            justifyContent="space-around"
            alignItems="center"
            height="fit-content"
            mx={10}>
                <Box className="earth"></Box>
                <Typography color="gray" my={3}>Recherche d'un joueur en cours...</Typography>
                { quote.content ?
                    <Typography align="center" className="quoteText">{ quote.content }<br />- { quote.author }</Typography>
                    :
                    <>
                        <Skeleton variant="text" width={300} className="loadingText" />
                        <Skeleton variant="text" width={150} className="loadingText" />
                    </>
                }
            </Stack>

            { canPlay && question ?
                <List
                className={ `${shake ? 'shake' : ''} quizList` }
                onAnimationEnd={() => setShake(false)}>
                    <ListItem disablePadding sx={{ px: 3, py: 1 }}>
                        <Typography sx={{ fontFamily: "'Raleway', sans-serif", width: "100%" }}>
                            <Stack flexDirection="row" justifyContent="space-between" mb={1}>
                                <Typography fontWeight="bold" className="raleway">Question :</Typography>
                                <Typography color="lightgray" className="raleway">Entraînement</Typography>
                            </Stack>
                            { question.question }
                        </Typography>
                    </ListItem>

                    { question.answers.map((proposition, i) => (
                        <ListItem key={i} disablePadding>
                            <ListItemButton onClick={() => handleAnswer(proposition)} sx={{ display: "flex", justifyContent: "center" }}>
                                <ListItemIcon>
                                    <ListNumberIcon>{ i + 1 }</ListNumberIcon>
                                </ListItemIcon>
                                <ListItemText className="raleway" primary={ proposition } />
                            </ListItemButton>
                        </ListItem>
                    )) }
                </List>
                :
                <Zoom in={!canPlay} unmountOnExit>
                    <Alert variant="outlined" severity="success" color="success" className="quizSuccess">
                        Bravo !<br />
                        Tu as terminé le quiz !<br />
                        Maintenant, termine ton adversaire&nbsp;&nbsp;🥷
                    </Alert>
                </Zoom>
            }
        </Stack>
    ) : (
        <Slide direction="left" in={props.launchFoundPlayersAnimation} timeout={1000} mountOnEnter unmountOnExit>
            <Stack justifyContent="center" alignItems="center" bgcolor="#3f50b5" width="100vw" height="100vh">
                <Typography className="playerFoundText bounce">
                    Joueur trouvé !
                </Typography>
            </Stack>
        </Slide>
    );
}

interface WaitingProps {
    launchFoundPlayersAnimation: boolean;
}

const ListNumberIcon = (props: ListNumberIconProps) => {
    return (
        <Box
        width={30}
        height={30}
        bgcolor="lightblue"
        color="white"
        borderRadius={50}
        display="flex"
        justifyContent="center"
        alignItems="center">
            <Typography>{ props.children }</Typography>
        </Box>
    );
}

interface ListNumberIconProps {
    children: ReactNode;
}

export default Waiting;