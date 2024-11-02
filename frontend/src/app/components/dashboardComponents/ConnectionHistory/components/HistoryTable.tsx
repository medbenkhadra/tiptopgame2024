import React, {useEffect, useState} from 'react';
import {ColumnsType} from "antd/es/table";
import {Col, ConfigProvider, Form, Row, Table, Tag} from "antd";
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";

import LogoutService from "@/app/service/LogoutService";
import {StopOutlined} from "@ant-design/icons";
import frFR from 'antd/lib/locale/fr_FR';

interface DataType {
    id: string;
    login_time: {
        date: string;
        time: string;
    };
    logout_time: {
        date: string;
        time: string;
    };
    user: {
        id: string;
        firstname: string;
        lastname: string;
        email: string;
        role: string;
    };
    is_active: boolean;
    duration: string;

}

interface storeManagersTableProps {
    selectedStoreId?: string | null;
    data: DataType;
}

export default function HistoryTable({selectedStoreId, data}: storeManagersTableProps) {




    const getRoleLabel = (role: string | null) => {
        switch (role) {
            case 'ROLE_ADMIN':
                return 'Administrateur';
            case 'ROLE_EMPLOYEE':
                return 'Employé (Caisse)';
            case 'ROLE_STOREMANAGER':
                return 'Manager de magasin';
            case 'ROLE_CLIENT':
                return 'Client';
            case 'ROLE_BAILIFF':
                return 'Espace Huissier';
            default:
                return 'Inconnu';
        }
    }


    const columns: ColumnsType<DataType> = [
        {
            width: "5%",
            title: '',
            key: 'id',
            dataIndex: 'id',
        },
        {
            width: "10%",
            title: 'Utilisateur',
            key: 'user',
            dataIndex: 'user',
            render: (user: any) => (
                <>
                    {
                        user ?
                            <span>{user.lastname} {user.firstname}</span>
                            :
                            <span>
                                <StopOutlined />
                            </span>
                    }
                </>
            ),
        },

        {
            width: "10%",
            title: 'Rôle',
            key: 'user',
            dataIndex: 'user',
            render: (user: any) => (
                <>
                    {
                        user ?
                            <span>{
                                getRoleLabel(user.role)
                            }</span>
                            :
                            <span>
                                <StopOutlined />
                            </span>
                    }
                </>
            ),
        },


        {
            width: "15%",
            title: 'Status',
            key: 'details',
            align: 'left',
            dataIndex: 'is_active',
            render: (is_active: any) => (
                <>
                    <span>
                        {
                        is_active ?
                        <Tag color="green">
                            Session active
                        </Tag>
                        :
                        <Tag color="red">
                            Session terminée
                        </Tag>
                        }

                    </span>
                </>
            ),
        },
        {
            width: "20%",
            title: 'Date de connexion',
            key: 'login_time',
            dataIndex: 'login_time',
            render: (login_time: any) => (
                <>
                    <span>Le {login_time.date} à {login_time.time}</span>
                </>
            ),
        },
        {
            width: "20%",
            title: 'Date de déconnexion',
            key: 'logout_time',
            dataIndex: 'logout_time',
            render: (logout_time: any) => (
                <>
                    {
                        logout_time.date ?
                            <span>Le {logout_time.date} à {logout_time.time}</span>
                            :
                            <span>
                                <StopOutlined />
                            </span>
                    }
                </>
            ),
        },
        {
            width: "10%",
            title: 'Durée',
            key: 'duration',
            dataIndex: 'duration',
            render: (duration: any) => (
                <>
                    <span>{duration}</span>
                </>
            ),
        },






    ];


    useEffect(() => {
        console.log("selectedStoreId", selectedStoreId);
        console.log("datadatadata", data);
    }, []);


    const customEmptyText = (
        <div className={styles.emptyTableTextDiv}>
            <span>Aucun historique d'action n'est disponible pour le moment.
            </span>
            <span><StopOutlined/></span>
        </div>
    );



    return (
        <>
            <Row className={`${styles.fullWidthElement}`}>
                <Col className={styles.fullWidthElement}>
                    <ConfigProvider locale={frFR}>
                        <Table
                            className={`${styles.tableProfileManagement} tableClientManagement`}
                            locale={{emptyText: customEmptyText}}
                            columns={columns}
                            rowKey={(record) => record.id}
                            dataSource={data as any}
                            pagination={false}
                        />
                    </ConfigProvider>
                </Col>
            </Row>





        </>


    );
}

