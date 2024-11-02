import React, {useState} from 'react';
import {
    AppstoreOutlined,
    BarcodeOutlined,
    BulbOutlined,
    ControlOutlined, CrownOutlined,
    DashboardOutlined, FieldTimeOutlined, FileSearchOutlined,
    GiftOutlined,
    GlobalOutlined,
    GoldOutlined,
    HistoryOutlined, LoginOutlined,
    MailOutlined,
    MenuFoldOutlined,
    MenuUnfoldOutlined, MessageOutlined,
    SettingOutlined,
    ShopOutlined,
    SketchOutlined, SolutionOutlined, SoundOutlined, TeamOutlined,
    UserOutlined
} from '@ant-design/icons';
import type {MenuProps} from 'antd';
import {Button, Menu, Row} from 'antd';
import style from '@/styles/pages/dashboards/storeAdminDashboard.module.css';

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



    getItem('Gestion des magasins', 'storesItem', <AppstoreOutlined />, [
        getItem('Magasins', 'storesManagementItem' , <ShopOutlined />),
        getItem('Gestion des Profils', 'profilesManagementItem' , <UserOutlined />),
    ]),

    getItem('Gestion des clients', 'clientsItem', <TeamOutlined /> , [
        getItem('Clients Inscrits', 'statisticItemClients' , <GlobalOutlined />),
        getItem('Participants Actifs ', 'statisticItemPrizes' , <SketchOutlined />),
    ]),

    getItem('Jeu-Concours', 'gameItem', <BulbOutlined />, [
        getItem('Historique des gains', 'historyPrizesItem' ,<SoundOutlined />),
        getItem('Historique des tickets', 'ticketsHistoryItem' , <FileSearchOutlined />),
        getItem('Tickets', 'ticketsItem' , <BarcodeOutlined />),
        getItem('Lots des gains', 'prizesLotsItem' , <GiftOutlined />),
        getItem('Badges de Récompenses', 'badgesItem' , <CrownOutlined />),
    ]),


    getItem('Règlement du Jeu', 'gameConfigItem', <ControlOutlined />, [
        getItem('Géneral', 'datesConfigItem' , <ControlOutlined />),
        getItem('Tirage au sort final', 'finalDrawItem' , <GoldOutlined />),
    ]),


    getItem('Historique générale', 'generalHistory', <HistoryOutlined />, [
        getItem('Historique des connexions', 'connectionsHistory' , <LoginOutlined />),
        getItem('Historique des actions' , 'actionHistory' , <FieldTimeOutlined />),
        getItem('Historique des e-mails' , 'emailsHistory' ,<SolutionOutlined />),


    ]),



    getItem('Paramètres', 'settingsItem', <SettingOutlined />, [
        getItem('Paramètres Généraux', 'generalSettingsItem' , <SettingOutlined />),
        getItem('Modèles E-mails', 'CorrespandancesTemplates' , <MailOutlined />),
    ]),

];


const rootSubmenuKeys = ['dashboardItem', 'storesItem', 'clientsItem' , 'gameItem', 'gameConfigItem', 'generalHistory' , 'settingsItem'];
interface SidebarProps {
    onMenuItemClick: (menuItemKey: string) => void;
    selectedMenuItem: string;
    toggleCollapsed: () => void;
    collapsed: boolean;
}

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
