import React, {Component, useEffect, useState} from 'react';
import Sidebar from "@/pages/dashboard/store_manager/components/sidebar";
import {Row,Col} from "antd";
import TopNavBar from "@/app/components/dashboardComponents/widgets/topNavBar";
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import "@/styles/pages/dashboards/globalDashboardStyle.css";
import StoresManagement from "@/app/components/dashboardComponents/StoreManagementComponent/StoresManagement";


import RedirectService from "@/app/service/RedirectService";
import ProfilesManagement from "@/app/components/dashboardComponents/ProfilesManagementComponent/ProfilesManagement";
import HomePage from "@/app/components/dashboardComponents/HomePageComponent/HomePageDashboard";
import TicketsPageDashboard from "@/app/components/dashboardComponents/TicketsPageComponent/TicketsPageDashboard";
import PrizesListPage from "@/app/components/dashboardComponents/PrizesPageComponent/PrizesListPage";
import ClientManagementPage
    from "@/app/components/dashboardComponents/ClientManagementComponents/ClientManagementPage";
import ParticipantManagementPage
    from "@/app/components/dashboardComponents/ClientManagementComponents/ParticipantManagementPage";
import GameGainHistoryPage from "@/app/components/dashboardComponents/GameGainHistory/GameGainHistoryPage";
import SpinnigLoader from "@/app/components/widgets/SpinnigLoader";
import GeneralSettingsTemplates
    from "@/app/components/dashboardComponents/GeneralSettingsComponents/GeneralSettingsTemplates";
import Head from "next/head";

function storeAdminDashboard() {

    const { redirectUserToHomePage } = RedirectService();


    const [selectedMenuItem, setSelectedMenuItem] = useState<string>("dashboardItem");

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

    const [userRrole , setUserRole] = useState<string | null>(null);
    const [loading , setLoading] = useState<boolean>(true);
    const [userToken , setUserToken] = useState<string | null>(null);
    useEffect(() => {
        setUserRole(localStorage.getItem('loggedInUserRole'));
        setUserToken(localStorage.getItem('loggedInUserToken'));
        if (userToken == null && userToken == "") {
            window.location.href = '/store_login';
        }
        setLoading(true)
    }, []);

    useEffect(() => {
        setLoading(true);
        if (userRrole == "ROLE_STOREMANAGER") {
            setLoading(false);
        }
        if (userRrole == "ROLE_EMPLOYEE") {
            window.location.href = '/dashboard/store_employee';
        }
        if (userRrole == "ROLE_CLIENT") {
            window.location.href = '/dashboard/client';
        }

        if (userRrole == "ROLE_ADMIN") {
            window.location.href = '/dashboard/storeAdmin';
        }

        if(userRrole == "ROLE_BAILIFF") {
            window.location.href = '/dashboard/store_bailiff';
        }

    }, [userRrole]);


    const toggleCollapsed = () => {
        setCollapsed(!collapsed);
    };

        return (

            <>
                <Head>
                    <title>TipTop - Tableau de bord - Gestionnaire</title>
                </Head>
                {loading && (
                    <SpinnigLoader></SpinnigLoader>
                )}
                {!loading && (
                    <>
            <div>

                <Row>
                    <Col md={collapsed ? '': 4 }>
                        <Sidebar collapsed={collapsed} toggleCollapsed={toggleCollapsed} onMenuItemClick={handleMenuItemClick} selectedMenuItem={selectedMenuItem}></Sidebar>
                    </Col>
                    <Col md={collapsed ? '': 20 } className={styles.mainPageDiv}>
                        <Row>
                            <TopNavBar></TopNavBar>
                        </Row>
                        <Row className={styles.mainContent}>
                            {selectedMenuItem==="dashboardItem" && <HomePage></HomePage>}
                            {selectedMenuItem==="storesManagementItem" && <StoresManagement></StoresManagement>}
                            {selectedMenuItem==="profilesManagementItem" && <ProfilesManagement></ProfilesManagement>}
                            {selectedMenuItem==="ticketsItem" && <TicketsPageDashboard></TicketsPageDashboard>}
                            {selectedMenuItem==="prizesLotsItem" && <PrizesListPage></PrizesListPage>}
                            {selectedMenuItem==="statisticItemClients" && <ClientManagementPage></ClientManagementPage>}
                            {selectedMenuItem==="statisticItemPrizes" && <ParticipantManagementPage></ParticipantManagementPage>}
                            {selectedMenuItem==="historyPrizesItem" && <GameGainHistoryPage></GameGainHistoryPage>}

                            {selectedMenuItem==="settingsItem" && <GeneralSettingsTemplates></GeneralSettingsTemplates>}

                        </Row>
                    </Col>
                </Row>
            </div>
            </>
                )}
            </>
        );

}

export default storeAdminDashboard;