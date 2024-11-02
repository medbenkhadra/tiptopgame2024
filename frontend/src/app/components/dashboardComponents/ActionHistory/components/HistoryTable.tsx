import React, {useEffect, useState} from 'react';
import {ColumnsType} from "antd/es/table";
import {Button, Col, ConfigProvider, Form, Row, Space, Table, Tag} from "antd";
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";

import LogoutService from "@/app/service/LogoutService";
import {InfoCircleOutlined, MinusOutlined, StopOutlined} from "@ant-design/icons";
import frFR from 'antd/lib/locale/fr_FR';
import Image from "next/image";
import InfuserImg from "@/assets/images/infuser.png";
import TeaBoxImg from "@/assets/images/teaBox.png";
import TeaBoxSignatureImg from "@/assets/images/teaBoxSignature.png";
import SurpriseBoxImg from "@/assets/images/surprise.png";
import SurprisePlusImg from "@/assets/images/surprisePlus.png";

interface DataType {
    id: string;
    details: string;
    action_type: string;
    created_at: {
        date: string;
        time: string;
    };
    user_done_action: {
        id: string;
        firstname: string;
        lastname: string;
        email: string;
        dob: string;
    };

    user_action_related_to: {
        id: string;
        firstname: string;
        lastname: string;
        email: string;
        dob: string;
    };

}

interface storeManagersTableProps {
    selectedStoreId?: string | null;
    data: DataType;
}

export default function HistoryTable({selectedStoreId, data}: storeManagersTableProps) {


    const {logoutAndRedirectAdminsUserToLoginPage} = LogoutService();
    const [formRef] = Form.useForm();

    const [selectedRow, setSelectedRow] = useState<DataType | null>(null);




    const getActionTypeColor = (action_type: string) => {
        switch (action_type) {
            case "Gestion des magasins":
                return "pink";
            case "Gestion des utilisateurs":
                return "blue";
            case "Gestion des comptes":
                return "purple";

            default:
                return "default";
        }
    }


    const columns: ColumnsType<DataType> = [
        {
            width: "10%",
            title: '',
            key: 'id',
            dataIndex: 'id',
        },
        {
            title: 'Utilisateur concerné',
            key: 'user_action_related_to',
            dataIndex: 'user_action_related_to',
            render: (user_action_related_to: any) => (
                <>
                    {
                        user_action_related_to ?
                            <span>{user_action_related_to.firstname} {user_action_related_to.lastname}</span>
                            :
                            <span>
                                <StopOutlined />
                            </span>
                    }
                </>
            ),
        },
        {
            width: "20%",
            title: 'Détails',
            key: 'details',
            dataIndex: 'details',
            align: 'left'
        },

        {
            title: 'Type d\'action',
            key: 'action_type',
            dataIndex: 'action_type',
            render: (action_type: any) => (
                <>
                                <Tag
                                    color={getActionTypeColor(action_type)}
                                    className={`${styles.tagActionType}`}
                                >
                                    <span>{action_type}</span>
                                </Tag>
                </>

            )
        },
        {
            width: "20%",
            title: 'Date de l\'action',
            key: 'created_at',
            dataIndex: 'created_at',
            render: (created_at: any) => (
                <>
                    <span>Le {created_at.date} à {created_at.time}</span>
                </>
            ),
        },
        {
            title: 'Utilisateur ayant effectué l\'action',
            key: 'user_done_action',
            dataIndex: 'user_done_action',
            render: (user_done_action: any) => (
                <>
                    <span>{user_done_action.firstname} {user_done_action.lastname}</span>
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

