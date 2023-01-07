import { createContext, useState, useEffect, ReactNode, Dispatch } from "react";
import { isAuthenticated } from "./AuthService";

const UserContext = createContext<UserContextInterface>([{ player_id: null, nickname: "", email: "", credential: "" }, () => null]);

export const UserProvider = (props: UserProviderProps) => {
    const [currentUser, setCurrentUser] = useState<User>({ player_id: null, nickname: "", email: "", credential: "" });

    useEffect(() => {
        const checkLoggedIn = async () => {
            let userLoggedIn = isAuthenticated();
            if (!userLoggedIn) {
                localStorage.setItem('user', '');
                userLoggedIn = "";
            }

            setCurrentUser(userLoggedIn);
        }

        checkLoggedIn();
    }, []);

  return (
    <UserContext.Provider value={[ currentUser, setCurrentUser ]}>
        { props.children }
    </UserContext.Provider>
  )
}

type User = {
    player_id: number | null;
    nickname: string;
    email: string;
    credential: string;
}

interface UserProviderProps {
    children: ReactNode;
}

type UserContextInterface = [User, Dispatch<User>];

export default UserContext;