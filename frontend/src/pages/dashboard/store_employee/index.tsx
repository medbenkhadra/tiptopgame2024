import React, {Component, useEffect, useState} from 'react';
import Sidebar from "@/pages/dashboard/store_employee/components/sidebar";
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
import PrintTicketsPage from "@/app/components/dashboardComponents/TicketsPageComponent/PrintTicketsPage";
import ConfirmTicketGain from "@/app/components/dashboardComponents/TicketsPageComponent/ConfirmTicketGain";
import GameGainHistoryPage from "@/app/components/dashboardComponents/GameGainHistory/GameGainHistoryPage";
import SpinnigLoader from "@/app/components/widgets/SpinnigLoader";
import GeneralSettingsTemplates
    from "@/app/components/dashboardComponents/GeneralSettingsComponents/GeneralSettingsTemplates";
import Head from "next/head";

function storeAdminDashboard() {



    const [selectedMenuItem, setSelectedMenuItem] = useState<string>("dashboardItem");

    useEffect(() => {
        const selectedMenuItemSaved = localStorage.getItem("selectedMenuItem");
        if (selectedMenuItemSaved) {
            setSelectedMenuItem(selectedMenuItemSaved);
        }
    }, [selectedMenuItem]);

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
            window.location.href = '/dashboard/store_manager';
        }
        if (userRrole == "ROLE_EMPLOYEE") {
            setLoading(false);
        }
        if (userRrole == "ROLE_CLIENT") {
            window.location.href = '/dashboard/client';
        }

        if (userRrole == "ROLE_ADMIN") {
            window.location.href = '/dashboard/store_admin';
        }

        if(userRrole == "ROLE_BAILIFF") {
            window.location.href = '/dashboard/store_bailiff';
        }


    }, [userRrole]);

    const handleMenuItemClick = (menuItemKey: string) => {
        setSelectedMenuItem(menuItemKey);
        localStorage.setItem("selectedMenuItem", menuItemKey);
    };

    const [collapsed, setCollapsed] = useState(false);

    const toggleCollapsed = () => {
        setCollapsed(!collapsed);
    };

        return (
            <>
                <Head>
                    <title>TipTop - Tableau de bord - Caissier</title>
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
                            {selectedMenuItem==="profilesManagementItem" && <ProfilesManagement></ProfilesManagement>}
                            {selectedMenuItem==="ticketsItem" && <TicketsPageDashboard></TicketsPageDashboard>}
                            {selectedMenuItem==="printTicketsItem" && <PrintTicketsPage></PrintTicketsPage>}

                            {selectedMenuItem==="prizesValidationItem" &&<ConfirmTicketGain></ConfirmTicketGain>}
                            {selectedMenuItem==="historyPrizesItem" && <GameGainHistoryPage></GameGainHistoryPage>}


                            {selectedMenuItem==="prizesLotsItem" && <PrizesListPage></PrizesListPage>}
                            {selectedMenuItem==="statisticItemClients" && <ClientManagementPage></ClientManagementPage>}
                            {selectedMenuItem==="statisticItemPrizes" && <ParticipantManagementPage></ParticipantManagementPage>}

                            {selectedMenuItem==="generalSettingsItem" && <GeneralSettingsTemplates></GeneralSettingsTemplates>}

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