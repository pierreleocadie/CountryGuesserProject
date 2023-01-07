import { Question } from "./interfaces/Question.interface";

export const getRandomQuestion = (): Question | null => {
    if (!questions.length) return null;

    const random = Math.floor(Math.random() * questions.length);
    const randomQuestion = questions[random];   // Question choisie aléatoirement
    
    questions.splice(random, 1);    // On la retire du tableau pour pas qu'elle ne réapparaisse

    return randomQuestion;
}

export const validateAnswer = (proposition: string, answer: string) => {
    return proposition === answer;
}

// Source : https://www.culturequizz.com/quiz/quiz-merveilles-du-monde/
export const questions: Question[] = [
    {
        question: "Dans lequel de ces pays, peut-on voir l'une des 7 nouvelles Merveilles du monde ?",
        answers: [
            "France", "Espagne", "Italie", "Grèce",
        ],
        answer: "Italie",
    },
    {
        question: "Quel est le seul monument toujours debout qui était dans le classement original des Merveilles du monde antique ?",
        answers: [
            "Les statues de l'Ile de Pâques", "Les pyramides de Gizeh", "Le temple d'Angkor", "La grande mosquée d'Istanbul",
        ],
        answer: "Les pyramides de Gizeh",
    },
    {
        question: "Dans quel pays se trouve Pétra, l'une des nouvelles Merveilles du monde ?",
        answers: [
            "Iran", "Jordanie", "Israël", "Turquie",
        ],
        answer: "Jordanie",
    },
    {
        question: "Quel est le nom du peuple ayant vécu à Pétra ?",
        answers: [
            "Nabatéens", "Turukkéens", "Phéniciens", "Amorrites"
        ],
        answer: "Nabatéens",
    },
    {
        question: "Dans quelle région du Pérou est situé la nouvelle Merveille du monde, le Machu Picchu ?",
        answers: [
            "Lima", "Apurimac", "Tacna", "Cuzco"
        ],
        answer: "Cuzco",
    },
    {
        question: "Quelle est la date estimée de la construction du Machu Picchu ?",
        answers: [
            "450", "1050", "1450", "1750"
        ],
        answer: "1450",
    },
    {
        question: "De quelle civilisation le Machu Picchu est-il une création ?",
        answers: [
            "Les Aztèques", "Les Mayas", "Les Zapotèques", "Les Incas"
        ],
        answer: "Les Incas",
    },
    {
        question: "Dans quel pays peut-on visiter le Taj Mahal ?",
        answers: [
            "Mexique", "Inde", "Maroc", "Chine",
        ],
        answer: "Inde",
    },
    {
        question: "Qu'est-ce que le Taj Mahal en Inde ?",
        answers: [
            "Un mausolée", "Un temple bouddhiste", "Un palais royal", "Une chapelle",
        ],
        answer: "Un mausolée",
    },
    {
        question: "Dans quel pays se situe la statue du Christ rédempteur, une des nouvelles Merveilles du monde ?",
        answers: [
            "Argentine", "Mexique", "Brésil", "Pérou"
        ],
        answer: "Brésil",
    },
    {
        question: "Quelle Merveille du monde antique aurait été détruite dans un tremblement de terre ?",
        answers: [
            "La statue chryséléphantine de Zeus à Olympie", "Le phare d'Alexandrie", "Les jardins suspendus de Babylone", "Le colosse de Rhodes",
        ],
        answer: "Le colosse de Rhodes",
    },
    {
        question: "À quel pharaon est dédiée la grande pyramide de Gizeh ?",
        answers: [
            "Khéops", "Ramsès II", "Toutânkhamon", "Akhénaton"
        ],
        answer: "Khéops",
    },
    {
        question: "Dans quel pays d’aujourd’hui était autrefois la ville de Babylone, où se trouvaient les jardins suspendus de Nabuchodonosor II ?",
        answers: [
            "Égypte", "Azerbaïdjan", "Irak", "Émirats arabes unis"
        ],
        answer: "Irak",
    },
    {
        question: "Quelle est la longueur de la Grande Muraille de Chine ?",
        answers: [
            "210 km", "1.200 km", "12.100 km", "21.200 km"
        ],
        answer: "21.200 km",
    },
    {
        question: "De quelle année date le classement des nouvelles Merveilles du monde ?",
        answers: [
            "1983", "1990", "2006", "2018"
        ],
        answer: "2006",
    },
];