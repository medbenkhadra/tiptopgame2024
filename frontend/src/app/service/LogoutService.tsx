'use client'
import {useRouter} from "next/router";

const LogoutService = () => {
    const router = useRouter();

    const logoutAndRedirectAdminsUserToLoginPage = () => {
        const userRole = localStorage.getItem('loggedInUserRole');
        localStorage.removeItem('loggedInUserToken');
        localStorage.removeItem('loggedInUserRole');
        localStorage.removeItem('loggedInUser');
        localStorage.removeItem('loggedInUserId');
        localStorage.removeItem('loggedInUserEmail');
        localStorage.removeItem('selectedMenuItem');
        localStorage.removeItem('firstLoginClientStatus');
        if(userRole === "ROLE_CLIENT"){
            router.push("/client_login");
        }else {
            router.push("/store_login");
        }
    }

    return {logoutAndRedirectAdminsUserToLoginPage};
};

export default LogoutService;
