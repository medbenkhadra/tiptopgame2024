import React, {Component, useEffect, useState} from 'react';
import Sidebar from "@/pages/dashboard/store_admin/components/sidebar";
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
import CorrespandancesTemplates
    from "@/app/components/dashboardComponents/CorrespandancesTemplatesComponents/CorrespandancesTemplates";
import GeneralSettingsTemplates
    from "@/app/components/dashboardComponents/GeneralSettingsComponents/GeneralSettingsTemplates";
import BadgesListPage from "@/app/components/dashboardComponents/BadgesPageComponent/BadgesListPage";
import ActionHistoryPage from "@/app/components/dashboardComponents/ActionHistory/ActionHistoryPage";
import TicketsHistory from "@/app/components/dashboardComponents/TicketsHistory/TicketsHistory";
import ConnectionHistory from "@/app/components/dashboardComponents/ConnectionHistory/ConnectionHistory";
import EmailingHistory from "@/app/components/dashboardComponents/EmailingHistory/EmailingHistory";
import GameSettingsTemplates from "@/app/components/dashboardComponents/GameSettingsComponent/GameSettingsTemplates";
import TirageAuSortTemplate from "@/app/components/dashboardComponents/TirageAuSortComponent/TirageAuSortTemplate";
import Head from 'next/head';
function storeAdminDashboard() {



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
            window.location.href = '/dashboard/store_employee';
        }
        if (userRrole == "ROLE_CLIENT") {
            window.location.href = '/dashboard/client';
        }

        if (userRrole == "ROLE_ADMIN") {
            setLoading(false);
        }

        if(userRrole == "ROLE_BAILIFF") {
            window.location.href = '/dashboard/store_bailiff';
        }


    }, [userRrole]);


    const [collapsed, setCollapsed] = useState(false);

    const toggleCollapsed = () => {
        setCollapsed(!collapsed);
    };

        return (
            <>
                <Head>
                    <title>TipTop - Tableau de bord - Administrateur</title>
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
                                    {selectedMenuItem==="CorrespandancesTemplates" && <CorrespandancesTemplates></CorrespandancesTemplates>}
                                    {selectedMenuItem==="generalSettingsItem" && <GeneralSettingsTemplates></GeneralSettingsTemplates>}
                                    {selectedMenuItem==="badgesItem" && <BadgesListPage></BadgesListPage>}
                                    {selectedMenuItem==="actionHistory" && <ActionHistoryPage></ActionHistoryPage>}
                                    {selectedMenuItem==="ticketsHistoryItem" && <TicketsHistory></TicketsHistory>}
                                    {selectedMenuItem==="connectionsHistory" && <ConnectionHistory></ConnectionHistory>}
                                    {selectedMenuItem==="emailsHistory" && <EmailingHistory></EmailingHistory>}
                                    {selectedMenuItem==="datesConfigItem" && <GameSettingsTemplates></GameSettingsTemplates>}
                                    {selectedMenuItem==="finalDrawItem" && <TirageAuSortTemplate></TirageAuSortTemplate>}





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