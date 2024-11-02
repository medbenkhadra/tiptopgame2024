import React, {useEffect, useState} from 'react';
import {ColumnsType} from "antd/es/table";
import {Col, ConfigProvider, Form, Row, Table, Tag} from "antd";
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";

import LogoutService from "@/app/service/LogoutService";
import {StopOutlined} from "@ant-design/icons";
import frFR from 'antd/lib/locale/fr_FR';


interface DataType {
    id: string;
    status: string;
    user: {
        id: string;
        firstname: string;
        lastname: string;
        email: string;
        role: string;
        store: {
            id: string;
            name: string;
            address: string;
            city: string;
            postalCode: string;
            country: string;
        }
    };
    employee: {
        id: string;
        firstname: string;
        lastname: string;
        email: string;
        role: string;
        store: {
            id: string;
            name: string;
            address: string;
            city: string;
            postalCode: string;
            country: string;
        }
    };
    updated_at: {
        date: string;
        time: string;
    };

    ticket: {
        id: string;
        ticket_code: string;
        prize: {
            id: string;
            label: string;
            name: string;

        };
        status: string;
        ticket_printed_at: {
            date: string;
            time: string;
        };
        win_date: {
            date: string;
            time: string;
        };
        ticket_generated_at: {
            date: string;
            time: string;
        };
    };


}


interface HistoryTableProps {
    selectedStoreId?: string | null;
    data: DataType;
}

export default function HistoryTable({selectedStoreId, data}: HistoryTableProps) {


    const {logoutAndRedirectAdminsUserToLoginPage} = LogoutService();
    const [formRef] = Form.useForm();

    const [selectedRow, setSelectedRow] = useState<DataType | null>(null);




    const getTagColor = (status: string) => {
        switch (parseInt(status)) {
            case 1:
                return "red";
            case 2:
                return "cyan";
            case 3:
                return "volcano";
            case 4:
                return "orange";
            case 5:
                return "purple";
            default:
                return "default";
        }
    }


    function getStatusLabel(status: any) {
        switch (parseInt(status)) {
            case 1:
                return "Génération du ticket";
            case 2:
                return "Impression du ticket";
            case 3:
                return "Utilisation du ticket";
            case 4:
                return "Recupération de Gain";
            case 5:
                return "Expiration du ticket";
            case 6:
                return "Annulation du ticket";
            default:
                return "Ticket généré";
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
            title: 'Client concerné',
            key: 'client',
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
            width: "20%",
            title: 'Ticket',
            key: 'ticket',
            dataIndex: 'ticket',
            render: (ticket: any) => (
                <>
                    {
                        ticket ?
                            <span>{ticket.ticket_code}</span>
                            :
                            <span>
                                <StopOutlined />
                            </span>
                    }
                </>
            ),
            align: 'left'
        },

        {
            title: 'Caissier concerné',
            key: 'employee',
            dataIndex: 'employee',
            render: (employee: any) => (
                <>
                    {
                        employee ?
                            <span>{employee.firstname} {employee.lastname}</span>
                            :
                            <span>
                                <StopOutlined />
                            </span>
                    }
                </>
            )
        },
        {
            width: "20%",
            title: 'Action effectuée',
            key: 'status',
            dataIndex: 'status',
            render: (status: any) => (
                <>
                    {
                        status ?
                            <Tag color={getTagColor(status)}>

                                {getStatusLabel(status)}


                            </Tag>
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
            title: 'Date de l\'action',
            key: 'updated_at',
            dataIndex: 'updated_at',
            render: (updated_at: any) => (
                <>
                    <span>Le {updated_at.date} à {updated_at.time}</span>
                </>
            ),
        },
        {
            title: 'Magasin concerné',
            key: 'employee_store',
            dataIndex: 'employee',
            render: (employee: any) => (
                <>
                    {
                        employee ?
                            <span>{employee.store.name}</span>
                            :
                            <span>
                                <StopOutlined />
                            </span>
                    }
                </>
            ),
        },





    ];

    const customEmptyText = (
        <div className={styles.emptyTableTextDiv}>
            <span>Aucune donnée disponible pour le moment
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

