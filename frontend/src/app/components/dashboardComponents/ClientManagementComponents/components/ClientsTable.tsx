import React, {useEffect, useState} from 'react';
import {ColumnsType} from "antd/es/table";
import {
    Button,
    Col,
    ConfigProvider,
    Row,
    Space,
    Table,
    Tag
} from "antd";
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";

import {
    InfoCircleOutlined, StopOutlined
} from "@ant-design/icons";


import frFR from 'antd/lib/locale/fr_FR';
interface DataType {
    id: string;
    firstname: string;
    lastname: string;
    gender: string;
    email: string;
    dateOfBirth: string;
    age: string;
    role: string;
    phone: string;
    status: string;

}



interface storeManagersTableProps {
    selectedStoreId: string;
    data: DataType ;
}

function ClientTable({selectedStoreId , data}: storeManagersTableProps) {





    const columns: ColumnsType<DataType> = [
        {
            title: 'Nom',
            dataIndex: 'lastname',

        },
        {
            title: 'Prénom',
            dataIndex: 'firstname',

        },
        {
            title: 'Email',
            dataIndex: 'email',
        },
        {
            title: 'Genre',
            dataIndex: 'gender',
            width: '20%',
        },
        {
            title: 'Age',
            dataIndex: 'age',
        },
        {
            title: 'Téléphone',
            dataIndex: 'phone',
        },
        {
            title: 'Status',
            dataIndex: 'status',
            render: (_, {status}) => (
                <>
                    {status == "1" && (
                        <Tag color={'green'} key={status}>
                            Ouvert
                        </Tag>
                    ) || status == "2" && (
                        <Tag color={'red'} key={status}>
                            Fermé
                        </Tag>
                    )}
                </>
            ),
        },
        {
            title: 'Action',
            key: 'action',
            render: (_, record) => (
                <>
                    <Space size="middle">

                        <Button onClick={() => {

                        }} className={`${styles.profilDetailsBtn}`} icon={<InfoCircleOutlined />} size={"middle"}>
                            Détails
                        </Button>

                    </Space>
                </>
            ),
        },

    ];







    const customEmptyText = (
        <div className={styles.emptyTableTextDiv}>
            <span>Aucun Client trouvé
            </span>
            <span><StopOutlined /></span>
        </div>
    );


    // @ts-ignore
    return (
        <>
            <Row className={`${styles.fullWidthElement}`}>
                <Col className={styles.fullWidthElement}>
                    <ConfigProvider locale={frFR}>
                    <Table
                        className={`${styles.tableProfileManagement} tableClientManagement`}
                        locale={{emptyText : customEmptyText}}
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

export default ClientTable;