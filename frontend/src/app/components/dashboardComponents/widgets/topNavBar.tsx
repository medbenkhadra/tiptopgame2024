import React, {useEffect, useState} from 'react';
import {LogoutOutlined} from '@ant-design/icons';
import type { MenuProps } from 'antd';
import { Menu } from 'antd';
import style from '@/styles/pages/dashboards/storeAdminDashboard.module.css';
import { Avatar, Space } from 'antd';
import RedirectService from "@/app/service/RedirectService";
import Image from "next/image";
import logoTeaImage from "@/assets/images/logoTea.png";
import {getUserProdileById} from "@/app/api";


interface User {
    firstname: string;
    lastname: string;
    email: string;
}


function TopNavBar() {

    const { redirectClientUserToLoginPage } = RedirectService();



    const [user, setUser] = useState<User | null>(null);
    const [userRole , setUserRole] = useState<string | null>(null);


    useEffect(() => {
        let userId:any = localStorage.getItem('loggedInUserId');
        getUserProdileById(userId).then((response) => {
            setUser(response.user);
            setUserRole(response.user.role);
        }).catch((err) => {
            console.log(err);
        })
}, []);


    const logout = () => {
        localStorage.removeItem('loggedInUser');
        localStorage.removeItem('loggedInUserId');
        localStorage.removeItem('loggedInUserRole');
        localStorage.removeItem('loggedInUserEmail');
        localStorage.removeItem('loggedInUserToken');
        localStorage.removeItem('selectedMenuItem');


        redirectClientUserToLoginPage(userRole);

    }


    const getRoleLabel = (role: string | null) => {
        switch (role) {
            case 'ROLE_ADMIN':
                return 'Espace Administrateur';
            case 'ROLE_EMPLOYEE':
                return 'Espace Employé (Caisse)';
            case 'ROLE_STOREMANAGER':
                return 'Espace Gérant';
            case 'ROLE_CLIENT':
                return 'Espace Client';
            case 'ROLE_BAILIFF':
                return 'Espace Huissier';
            default:
                return 'Inconnu';
        }
    }

    let items: MenuProps['items'] = [
        {
            className: `${style.profileTopNavBarItem}`,
            label: <div className={style.unhoverableElement}>
                <Avatar className={style.unhoverableElement} src="https://images.prismic.io/utopix-next-website/Mzk0NGJkOWEtY2ZlYS00MjVjLTkwNTAtOGY5OWQzN2IzNGVi_762cec57-2eaf-4eaf-9a0d-2e7860147e48_profilhomme7.jpg?ixlib=js-3.8.0&w=3840&auto=format&fit=max" />
                { user && <span className={style.unhoverableElement}> {user.firstname} {user.lastname} </span>}
            </div>,
            key: 'avatar',
        },
        {
            label: <span onClick={logout} className={`${style.logoutSpan} logoutLink`}>Déconnexion</span>,
            key: 'SubMenu',
            icon:  <LogoutOutlined className={`${style.logoutSpan} logoutLink`} />,
            className: `${style.logoutTopNavBarItem} logoutLink`
        },

    ];

    const [current, setCurrent] = useState('mail');

    const onClick: MenuProps['onClick'] = (e) => {
        console.log('click ', e);
        if (e.key === 'avatar') return;
        setCurrent(e.key);
    };

    return (
        <div className={style.topNavBarDiv}>
            <a href="/">
            <div className={`${style.logoDivSideBar}`}>
                <strong>TipTop</strong>
                <Image className={`${style.logoSideBar}`} alt={"LogoTipTop"} src={logoTeaImage} />
            </div>
            </a>
            <div className={`w-100 justify-content-center ${style.roleTopNavBar}`}>
                { user && <span className={style.unhoverableElement}> {getRoleLabel(userRole)} </span>}
            </div>
            <Menu onClick={onClick} selectedKeys={[current]} mode="horizontal" items={items} />
        </div>
    );
}

export default TopNavBar;
