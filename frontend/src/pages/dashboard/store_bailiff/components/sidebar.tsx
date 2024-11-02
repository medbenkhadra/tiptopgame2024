import React, {useState} from 'react';
import {
    ApartmentOutlined,
    BarcodeOutlined,
    FileSearchOutlined,
    GiftOutlined,
    GlobalOutlined,
    HistoryOutlined,
    MenuFoldOutlined,
    MenuUnfoldOutlined,
    SketchOutlined
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
    getItem('Tirage au sort', 'tirageAuSort', <ApartmentOutlined />),

    getItem('Historique des gains', 'historyPrizesItem' ,<HistoryOutlined />),
    getItem('Historique des tickets', 'ticketsHistoryItem' , <FileSearchOutlined />),
    getItem('Tickets', 'ticketsItem' , <BarcodeOutlined />),
    getItem('Lots des gains', 'prizesLotsItem' , <GiftOutlined />),
    getItem('Clients Inscrits', 'statisticItemClients' , <GlobalOutlined />),
    getItem('Participants Actifs ', 'statisticItemPrizes' , <SketchOutlined />),





];


const rootSubmenuKeys = ['tirageAuSort', 'historyPrizesItem', 'ticketsHistoryItem', 'ticketsItem', 'prizesLotsItem', 'statisticItemClients', 'statisticItemPrizes'];
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
