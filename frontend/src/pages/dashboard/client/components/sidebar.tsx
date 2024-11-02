import React, {useEffect, useState} from 'react';
import {
    BarcodeOutlined,
    BulbOutlined, CrownOutlined,
    DashboardOutlined,
    GiftOutlined,
    HistoryOutlined,
    MenuFoldOutlined,
    MenuUnfoldOutlined,
    PieChartOutlined,
    PlayCircleOutlined,
    SettingOutlined,
} from '@ant-design/icons';

import type {MenuProps} from 'antd';
import {Button, Col, Menu, Row} from 'antd';
import style from '@/styles/pages/dashboards/storeAdminDashboard.module.css';
import {getClientBadges} from "@/app/api";
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import Image from "next/image";
import LevelOneImg from "@/assets/images/levels/level1.png";
import LevelTwoImg from "@/assets/images/levels/level2.png";
import LevelThreeImg from "@/assets/images/levels/level3.png";
import LevelFourImg from "@/assets/images/levels/level4.png";
import LevelFiveImg from "@/assets/images/levels/level5.png";

type MenuItem = Required<MenuProps>['items'][number];

function getItem(
    label: React.ReactNode,
    key: React.Key,
    icon?: React.ReactNode,
    children?: MenuItem[],
    type?: 'group',
): MenuItem {
    return {
        key,
        icon,
        children,
        label,
        type,
    } as MenuItem;
}

const items: MenuItem[] = [
    getItem('Tableau de bord', 'dashboardItem', <DashboardOutlined />),
    getItem('Tentez Votre Chance', 'playGameItem', <PlayCircleOutlined />),
    getItem('Jeu-Concours', 'gameItem', <BulbOutlined />, [
        getItem('Tickets associés', 'ticketsItem' , <BarcodeOutlined />),
        getItem('Historiques des gains', 'historyPrizesItem' , <HistoryOutlined />),
        getItem('Badges de Récompenses', 'badgesItem' , <CrownOutlined />),
        getItem('Lots des gains', 'prizesLotsItem' , <GiftOutlined />),
    ]),




getItem('Statistiques des gains', 'statisticsItem',<PieChartOutlined /> ),
    getItem('Paramètres du compte', 'settingsItem', <SettingOutlined />),

];


const rootSubmenuKeys = ['dashboardItem', 'playGameItem', 'gameItem', 'statisticsItem', 'settingsItem'];
interface SidebarProps {
    onMenuItemClick: (menuItemKey: string) => void;
    selectedMenuItem: string;
    toggleCollapsed: () => void;
    collapsed: boolean;
}

interface DataType {
    'id' : string;
    'name' : string;
    'description' : string;
}

const DataTypeDefault : DataType = {
    'id' : '',
    'name' : '',
    'description' : '',
};


function Sidebar({ onMenuItemClick, selectedMenuItem , toggleCollapsed , collapsed }: SidebarProps) {

    const [openKeys, setOpenKeys] = useState<string[]>([]);



    const onOpenChange: MenuProps['onOpenChange'] = (keys) => {
        const latestOpenKey = keys.find((key) => openKeys.indexOf(key) === -1);
        if (latestOpenKey && rootSubmenuKeys.indexOf(latestOpenKey) === -1) {
            setOpenKeys([]);
            setOpenKeys(keys);
        } else {
            setOpenKeys([]);
            setOpenKeys(latestOpenKey ? [latestOpenKey] : []);
        }
    };


    const [userRole, setUserRole] = useState("");
    const [clientBadge, setClientBadge] = useState<DataType>(DataTypeDefault);

    useEffect(() => {
        setUserRole(localStorage.getItem('loggedInUserRole') ?? "");
    }, []);

    useEffect(() => {
        let userId = localStorage.getItem('loggedInUserId') ?? "";
        if (userRole == "ROLE_CLIENT" && userId != "") {
            getClientBadges(userId).then((response) => {
                if (response != null){
                    if (response.badges != null){
                        if (response.badges.length > 0){
                            setClientBadge(response.badges[0]);
                        }
                    }
                }

            }).catch((err) => {
                console.log(err);
            });
        }

    }, [userRole]);


    useEffect(() => {
        console.log(clientBadge , "clientBadge");
    }, [clientBadge]);



    const renderBadgeImage = (badgeId: string) => {
        switch (badgeId.toString()) {
            case "1":
                return (
                    <Image src={LevelOneImg} alt={"LevelOneImg"}></Image>
                );
            case "2":
                return (
                    <Image src={LevelTwoImg} alt={"LevelTwoImg"}></Image>
                );
            case "3":
                return (
                    <Image src={LevelThreeImg} alt={"LevelThreeImg"}></Image>
                );
            case "4":
                return (
                    <Image src={LevelFourImg} alt={"LevelFourImg"}></Image>
                );
            case "5":
                return (
                    <Image src={LevelFiveImg} alt={"LevelFiveImg"}></Image>
                );
            default:
                return (<></>);
        }
    }
    let getTooltipStyle = (index: number) => {
        switch (index-1) {
            case 0:
                return {
                    color: "#ffffff",
                    cursor: 'pointer',
                    backgroundColor: "#212227",
                    fontSize: 10,
                    marginLeft: 5,
                    marginRight: 5,
                };
            case 1:
                return {
                    color: "#ffffff",
                    backgroundColor: "#E3E94B",
                    cursor: 'pointer',
                    fontSize: 10,
                    marginLeft: 5,
                    marginRight: 5,
                };
            case 2:
                return {
                    color: "#ffffff",
                    backgroundColor: "#FFA400",
                    cursor: 'pointer',
                    fontSize: 10,
                    marginLeft: 5,
                    marginRight: 5,
                };
            case 3:
                return {
                    color: "#ffffff",
                    cursor: 'pointer',
                    backgroundColor: "#7BC558",
                    fontSize: 10,
                    marginLeft: 5,
                    marginRight: 5,
                };
            case 4:
                return {
                    color: "#ffffff",
                    backgroundColor: "#EBB3E6",
                    cursor: 'pointer',
                    fontSize: 10,
                    marginLeft: 5,
                    marginRight: 5,
                };
            default:
                return {
                    color: "#ffffff",
                    backgroundColor: "#EBB3E6",
                    cursor: 'pointer',
                    fontSize: 10,
                    marginLeft: 5,
                    marginRight: 5,
                };
        }
    }


    return (
        <div className={style.sideBarDiv}>
            <Row className={style.toggleBtnDiv}>

                    <Button className={style.collapseBtn} onClick={toggleCollapsed}>
                        {collapsed ? <MenuUnfoldOutlined className={style.collapseBtnIcon} /> : <MenuFoldOutlined className={style.collapseBtnIcon} />}
                    </Button>
            </Row>
        <Menu
            key={selectedMenuItem}
            className={`${style.sideBarMenu} sideBarMenuDashboards`}
            mode="inline"
            openKeys={openKeys}
            onOpenChange={onOpenChange}
            inlineCollapsed={collapsed}
            items={items}
            defaultSelectedKeys={[`${selectedMenuItem}`]}
            onClick={({ key }) => onMenuItemClick(key)}
        />


        </div>
    );
}

export default Sidebar;
