import React, {useEffect, useState} from 'react';
import {ColumnsType} from "antd/es/table";
import {Col, ConfigProvider, Form, Row, Table, Tag} from "antd";
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";

import LogoutService from "@/app/service/LogoutService";
import {StopOutlined} from "@ant-design/icons";
import frFR from 'antd/lib/locale/fr_FR';

interface DataType {
    id: string;
    sent_at: {
        date: string;
        time: string;
    };

    receiver: {
        id: string;
        firstname: string;
        lastname: string;
        email: string;
        role: string;
        store: {
            id: string;
            name: string;

        }
    };
    service: {
        id: string;
        name: string;
        label: string;
        description: string;

    };
}

interface historyTableProps {
    data: DataType;
}

export default function HistoryTable({data}: historyTableProps) {

    function getTagColor(id:any) {
        id = id.toString();
        if(id == "1"){
            return "blue";
        }
        if(id === "2"){
            return "green";
        }

        if(id === "3"){
            return "orange";
        }
        if(id === "4"){
            return "red";
        }

        if(id === "5"){
            return "cyan";
        }

        if(id === "6"){
            return "purple";
        }

        if(id === "7"){
            return "magenta";
        }

        if(id === "8"){
            return "geekblue";
        }

        if(id === "9"){
            return "gold";
        }

        if(id === "10"){
            return "lime";
        }

        if(id === "11"){
            return "volcano";
        }

        if(id === "12"){
            return "yellow";
        }

        if(id === "13"){
            return "default";
        }

        return "purple";
    }



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
            title: 'Destinataire',
            key: 'receiver',
            dataIndex: 'receiver',
            render: (receiver: any) => (
                <>
                    {
                        receiver ?
                            <span>{receiver.lastname} {receiver.firstname}</span>
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
            dataIndex: 'receiver',
            render: (receiver: any) => (
                <>
                    {getRoleLabel(receiver.role)}
                </>
            ),
        },

        {
            width: "10%",
            title: 'E-mail',
            key: 'user',
            dataIndex: 'receiver',
            render: (receiver: any) => (
                <>
                    {receiver.email}
                </>
            ),
        },



        {
            width: "20%",
            title: 'Date d\'envoi',
            key: 'sent_at',
            dataIndex: 'sent_at',
            render: (sent_at: any) => (
                <>
                    <span>Le {sent_at.date} à {sent_at.time}</span>
                </>
            ),
        },

        {
            key: 'variables-template',
            title: 'Service',
            dataIndex: 'service',
            render: (service) => <>
                {service && <Tag
                    color={getTagColor(service.id)}


                >{
                    service.label
                }</Tag>}
                {!service && <Tag color="red">Aucun service</Tag>}
            </>,
        },

        {
            width: "15%",
            title: 'Magasin',
            key: 'store',
            dataIndex: 'receiver',
            render: (receiver: any) => (
                <>
                    {receiver.store?.name ?? <StopOutlined />}
                </>
            ),
        }


    ];



    const customEmptyText = (
        <div className={styles.emptyTableTextDiv}>
            <span>Aucun historique d'e-mails pour le moment</span>

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

