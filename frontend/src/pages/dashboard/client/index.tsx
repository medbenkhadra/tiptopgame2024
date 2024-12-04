import React, {useEffect, useState} from 'react';
import RedirectService from '../../../app/service/RedirectService';

import Sidebar from "@/pages/dashboard/client/components/sidebar";
import TopNavBar from "@/app/components/dashboardComponents/widgets/topNavBar";

import {Col, Row} from "antd";
import styles from "@/styles/pages/dashboards/clientDashboard.module.css";
import "@/styles/pages/dashboards/globalDashboardStyle.css";
import ClientHomePage from "@/app/components/dashboardComponents/HomePageComponent/HomePageDashboard";
import StoresManagement from "@/app/components/dashboardComponents/StoreManagementComponent/StoresManagement";
import ProfilesManagement from "@/app/components/dashboardComponents/ProfilesManagementComponent/ProfilesManagement";
import TicketsPageDashboard from "@/app/components/dashboardComponents/TicketsPageComponent/TicketsPageDashboard";
import PrizesListPage from "@/app/components/dashboardComponents/PrizesPageComponent/PrizesListPage";
import ClientManagementPage from "@/app/components/dashboardComponents/ClientManagementComponents/ClientManagementPage";
import ParticipantManagementPage
    from "@/app/components/dashboardComponents/ClientManagementComponents/ParticipantManagementPage";
import PlayGameComponent from "@/pages/dashboard/client/components/PlayGameComponent";

import SpinnigLoader from "@/app/components/widgets/SpinnigLoader";
import GameGainHistoryPage from "@/app/components/dashboardComponents/GameGainHistory/GameGainHistoryPage";
import GeneralSettingsTemplates
    from "@/app/components/dashboardComponents/GeneralSettingsComponents/GeneralSettingsTemplates";
import BadgesListPage from "@/app/components/dashboardComponents/BadgesPageComponent/BadgesListPage";
import Head from "next/head";


function ClientDashboard() {

    const { redirectUserToHomePage } = RedirectService();
    const [loading, setLoading] = useState(true);

    const [selectedMenuItem, setSelectedMenuItem] = useState<string>("playGameItem");

    useEffect(() => {
        const selectedMenuItemSaved = localStorage.getItem("selectedMenuItem");
        if (selectedMenuItemSaved) {
            setSelectedMenuItem(selectedMenuItemSaved);
        }
    }, [selectedMenuItem]);

    const handleMenuItemClick = (menuItemKey: string) => {
        setSelectedMenuItem(menuItemKey);
        localStorage.setItem("selectedMenuItem", menuItemKey);
    };

    const [collapsed, setCollapsed] = useState(false);

    const toggleCollapsed = () => {
        setCollapsed(!collapsed);
    };

    useEffect(() => {
        const firstLoginClientStatus = localStorage.getItem('firstLoginClientStatus');
        if (firstLoginClientStatus == "true") {
            window.location.href = '/dashboard/client/favorite_store_selection';
        }else {
            setLoading(false);
        }
    }, []);


    const [userRole , setUserRole] = useState<string | null>(null);
    const [userToken , setUserToken] = useState<string | null>(null);
    useEffect(() => {

        const storedUserRole = localStorage.getItem('loggedInUserRole');
        const storedUserToken = localStorage.getItem('loggedInUserToken');

        if(storedUserRole && storedUserToken){
            setUserRole(storedUserRole);
            setUserToken(storedUserToken);
        }

        setLoading(false)
    }, []);

    useEffect(() => {
        console.log("loading", loading);
        console.log("userToken", userToken);
        console.log("userRole", userRole);
        if (!loading && !userToken && !userRole) {
            window.location.href = '/client_login';
        }
    }, [loading, userToken, userRole]);


    useEffect(() => {
        setLoading(true);

        if(!loading && userRole){
            if (userRole == "ROLE_STOREMANAGER") {
                window.location.href = '/dashboard/store_manager';
            }
            if (userRole == "ROLE_EMPLOYEE") {
                window.location.href = '/dashboard/store_employee';
            }
            if (userRole == "ROLE_CLIENT") {
                setLoading(false);
            }

            if (userRole == "ROLE_ADMIN") {
                window.location.href = '/dashboard/store_admin';
            }

            if(userRole == "ROLE_BAILIFF") {
                window.location.href = '/dashboard/store_bailiff';
            }
        }else {
            setLoading(false);
        }

        }, [loading , userRole]);

    return (
        <>
            <Head>
                <title>TipTop - Tableau de bord - Client</title>
            </Head>
            {loading && (
                <SpinnigLoader></SpinnigLoader>
            )}
            {!loading && (
                <>
        <div>
            {loading && <SpinnigLoader></SpinnigLoader>}

            {!loading &&
            <Row>
                <Col md={collapsed ? '': 4 } className={`${styles.sideBarDiv}`}>
                    <Sidebar collapsed={collapsed} toggleCollapsed={toggleCollapsed} onMenuItemClick={handleMenuItemClick} selectedMenuItem={selectedMenuItem}></Sidebar>
                </Col>
                <Col md={collapsed ? '': 20 } className={styles.mainPageDiv}>
                    <Row>
                        <TopNavBar></TopNavBar>
                    </Row>
                    <Row className={styles.mainContent}>
                        {selectedMenuItem==="dashboardItem" && <ClientHomePage></ClientHomePage>}
                        {selectedMenuItem==="storesManagementItem" && <StoresManagement></StoresManagement>}
                        {selectedMenuItem==="profilesManagementItem" && <ProfilesManagement></ProfilesManagement>}
                        {selectedMenuItem==="ticketsItem" && <TicketsPageDashboard></TicketsPageDashboard>}
                        {selectedMenuItem==="prizesLotsItem" && <PrizesListPage></PrizesListPage>}
                        {selectedMenuItem==="statisticItemClients" && <ClientManagementPage></ClientManagementPage>}
                        {selectedMenuItem==="statisticItemPrizes" && <ParticipantManagementPage></ParticipantManagementPage>}
                        {selectedMenuItem==="playGameItem" && <PlayGameComponent></PlayGameComponent>}
                        {selectedMenuItem==="historyPrizesItem" && <GameGainHistoryPage></GameGainHistoryPage>}
                        {selectedMenuItem==="settingsItem" && <GeneralSettingsTemplates></GeneralSettingsTemplates>}
                        {selectedMenuItem==="badgesItem" && <BadgesListPage></BadgesListPage>}

                    </Row>
                </Col>
            </Row>
            }
        </div>
                </>
            )}
        </>
    );

}

export default ClientDashboard;


