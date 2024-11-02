import React, {useEffect, useState} from 'react';
import {ColumnsType} from "antd/es/table";
import {Button, Card, Col, ConfigProvider, Form, Modal, Row, Space, Table, Tag} from "antd";
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";

import LogoutService from "@/app/service/LogoutService";
import {
    BarcodeOutlined,
    GiftOutlined,
    InfoCircleOutlined,
    PrinterOutlined,
    ShopOutlined,
    StopOutlined,
    UserOutlined
} from "@ant-design/icons";
import frFR from 'antd/lib/locale/fr_FR';
import Image from "next/image";
import InfuserImg from "@/assets/images/infuser.png";
import TeaBoxImg from "@/assets/images/teaBox.png";
import TeaBoxSignatureImg from "@/assets/images/teaBoxSignature.png";
import SurpriseBoxImg from "@/assets/images/surprise.png";
import SurprisePlusImg from "@/assets/images/surprisePlus.png";

interface DataType {
    status: string;
    id: string;
    ticket_code: string;
    win_date: {
        date: string;
        time: string;
    };
    ticket_generated_at: {
        date: string;
        time: string;
    };
    ticket_printed_at: {
        date: string;
        time: string;
    };
    updated_at: {
        date: string;
        time: string;
    };

    employee: {
        id: string;
        firstname: string;
        lastname: string;
        email: string;
        dateOfBirth: string;
        phone: string;
    };

    user: {
        id: string;
        firstname: string;
        lastname: string;
        email: string;
        dateOfBirth: string;
        phone: string;
    };

    store: {
        id: string;
        name: string;
        address: string;
        phone: string;
        email: string;
        siren: string;
        postal_code: string;
        city: string;
        country: string;
    };

    prize: {
        id: string;
        name: string;
        label: string;
        prize_value: string;
        winning_rate: string;
    };

}


interface storeManagersTableProps {
    selectedStoreId?: string | null;
    data: DataType;
}

function GainsTable({selectedStoreId, data}: storeManagersTableProps) {


    const {logoutAndRedirectAdminsUserToLoginPage} = LogoutService();
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [formRef] = Form.useForm();

    const [selectedRow, setSelectedRow] = useState<DataType | null>(null);

    const openDetailsModal = (record: DataType) => {
        setSelectedRow(record);
        setIsModalOpen(true);
    };


    const handleCancel = () => {
        setIsModalOpen(false);
    };

    const renderPrizeImage = (prizeId: any) => {
        if (!prizeId) return (<></>);
        switch (prizeId.toString()) {
            case "1":
                return (
                    <Image className={`${styles.gainTableImg}`} src={InfuserImg} alt={"Infuseur"}></Image>
                );
            case "2":
                return (
                    <Image className={`${styles.gainTableImg}`} src={TeaBoxImg} alt={"Infuseur"}></Image>
                );
            case "3":
                return (
                    <Image className={`${styles.gainTableImg}`} src={TeaBoxSignatureImg} alt={"Infuseur"}></Image>
                );
            case "4":
                return (
                    <Image className={`${styles.gainTableImg}`} src={SurpriseBoxImg} alt={"Infuseur"}></Image>
                );
            case "5":
                return (
                    <Image className={`${styles.gainTableImg}`} src={SurprisePlusImg} alt={"Infuseur"}></Image>
                );
            default:
                return (<></>);
        }
    }


    const columns: ColumnsType<DataType> = [
        {
            title: '',
            key: 'action',
            render: (_, record, index) => (
                <>
                    <Space size="middle">
                        <span>{index + 1}</span>
                        <Button onClick={() => {
                            openDetailsModal(record);
                        }} className={`${styles.profilDetailsBtn}`} icon={<InfoCircleOutlined/>} size={"middle"}>
                            Détails
                        </Button>

                    </Space>
                </>
            ),
        },

        {
            title: 'Magasin',
            dataIndex: 'store',
            render: (_, record) => (
                <>
                <span>
              {record.store.name}
            </span>
                    <br/>
                    <small>{record.store.siren}</small>

                </>
            ),
        },
        {
            title: 'Ticket',
            dataIndex: 'ticket',
            render: (_, record) => (
                <span>
              #{record.ticket_code}
            </span>
            ),
        },
        {
            title: 'Participant',
            dataIndex: 'user',
            render: (_, record) => (
                <span>
              {record.user.lastname} {record.user.firstname}
            </span>
            ),
        },
        {
            title: 'Caisser',
            dataIndex: 'employee',
            render: (_, record) => (
                <span>
              {record.employee.lastname} {record.employee.firstname}
            </span>
            ),
        },
        {
            title: 'Gain',
            dataIndex: 'prize',
            render: (_, record) => (
                <span>
              {record.prize.label}
            </span>
            ),
        },
        {
            title: 'Status',
            dataIndex: 'status',
            render: (_, {status}) => (
                <>
                    <Tag color={'green'} key={status}>
                        Remis
                    </Tag>

                </>
            ),
        },
        {
            title: '',
            dataIndex: 'prize',
            render: (_, record) => (
                <>
                    {renderPrizeImage(record.prize.id)}
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
            <span>Aucun Gain trouvé
            </span>
            <span><StopOutlined/></span>
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
                            locale={{emptyText: customEmptyText}}
                            columns={columns}
                            rowKey={(record) => record.id}
                            dataSource={data as any}
                            pagination={false}
                        />
                    </ConfigProvider>
                </Col>
            </Row>

            <Modal
                className={`${styles.modalDetails} detailsModalGain`}
                title={
                    <>
                    <span className={`${styles.modalTitle}`}>
                        Détails du gain
                    </span>
                    </>
                }
                centered
                open={isModalOpen}
                onOk={() => setIsModalOpen(false)}
                onCancel={() => setIsModalOpen(false)}
                width={1000}
            >
                <Card className={`${styles.InfoCards} infoHistoryCard`} title={
                    <span>
                    <BarcodeOutlined style={{marginRight: 8}}/> Ticket #{selectedRow?.ticket_code}
                    </span>
                }>
                    <div className={`${styles.InfoCardLeft}`}>
                        <p>
                            <strong>Date de génération
                                : <br/>
                            </strong><span>{selectedRow?.ticket_generated_at.date} à {selectedRow?.ticket_generated_at.time} </span>
                        </p>
                        <p>
                            <strong>Date d'impression
                                : <br/></strong><span>{selectedRow?.ticket_printed_at.date} à {selectedRow?.ticket_printed_at.time}</span>
                        </p>
                    </div>
                    <div className={`${styles.InfoCardRight}`}>
                        <p>
                            <strong>Date de gain
                                : <br/></strong><span>{selectedRow?.win_date.date} à {selectedRow?.win_date.time}</span>
                        </p>
                        <p>
                            <strong>Date de remise
                                : <br/></strong><span>{selectedRow?.updated_at.date} à {selectedRow?.updated_at.time}</span>
                        </p>

                    </div>
                </Card>

                <Card title={
                    <span>
          <GiftOutlined style={{marginRight: 8}}/> Récompense
        </span>
                } className={`${styles.InfoCards} infoHistoryCard`} style={{marginTop: 16}}>
                    <div className={`${styles.InfoCardLeft}`}>
                        <p>
                            <strong>Gain :</strong><span>{selectedRow?.prize.label}</span>
                        </p>
                        <p>
                            <strong>Date de gain :</strong><span>{selectedRow?.win_date.date}</span>
                        </p>
                        <p>
                            <strong>Date de remise :</strong><span>{selectedRow?.updated_at.date}</span>
                        </p>
                    </div>
                    <div className={`${styles.InfoCardRight}`}>
                        {renderPrizeImage(selectedRow?.prize.id)}
                    </div>
                </Card>
                <Card className={`${styles.InfoCards} infoHistoryCard`} title={
                    <span>
          <UserOutlined style={{marginRight: 8}}/> Participant
        </span>
                } style={{marginTop: 16}}>
                    <div className={`${styles.InfoCardLeft}`}>
                        <p>
                            <strong>Nom Prénom
                                : <br/></strong><span>{selectedRow?.user.lastname} {selectedRow?.user.firstname}</span>
                        </p>
                        <p>
                            <strong>Date de naissance
                                : <br/></strong><span>{selectedRow?.employee.dateOfBirth}</span>
                        </p>
                        <p>
                            <strong>Email : <br/></strong><span>{selectedRow?.user.email}</span>
                        </p>
                    </div>
                    <div className={`${styles.InfoCardRight}`}>
                        <p>
                            <strong>Identifiant : <br/></strong><span>#{selectedRow?.user.id}</span>
                        </p>
                        <p>
                            <strong>Téléphone : <br/></strong><span>{selectedRow?.user.phone}</span>
                        </p>

                    </div>
                </Card>

                <Card className={`${styles.InfoCards} infoHistoryCard`}
                      title={
                          <span>
                              <PrinterOutlined style={{marginRight: 8}}/> Caissier
                          </span>
                      }
                      style={{marginTop: 16}}>
                    <div className={`${styles.InfoCardLeft}`}>
                        <p>
                            <strong>Nom Prénom
                                : <br/></strong><span>{selectedRow?.employee.lastname} {selectedRow?.employee.firstname}</span>
                        </p>
                        <p>
                            <strong>Email : <br/></strong><span>{selectedRow?.employee.email}</span>
                        </p>
                    </div>
                    <div className={`${styles.InfoCardRight}`}>
                        <p>
                            <strong>Identifiant : <br/></strong><span>#{selectedRow?.employee.id}</span>
                        </p>
                        <p>
                            <strong>Téléphone : <br/></strong><span>{selectedRow?.employee.phone}</span>
                        </p>

                    </div>
                </Card>


                <Card className={`${styles.InfoCards} infoHistoryCard`} title={
                    <span>
                              <ShopOutlined style={{marginRight: 8}}/> Magasin
                          </span>
                } style={{marginTop: 16}}>
                    <div className={`${styles.InfoCardLeft}`}>
                        <p>
                            <strong>Magasin : <br/></strong><span>{selectedRow?.store.name}</span>
                        </p>
                        <p>
                            <strong>Adresse : <br/></strong><span>{selectedRow?.store.address}</span>
                        </p>
                        <p>
                            <strong>Téléphone : <br/></strong><span>{selectedRow?.store.phone}</span>
                        </p>
                        <p>
                            <strong>Email : <br/></strong><span>{selectedRow?.store.email}</span>
                        </p>
                    </div>
                    <div className={`${styles.InfoCardRight}`}>
                        <p>
                            <strong>SIREN : <br/></strong><span>{selectedRow?.store.siren}</span>
                        </p>
                        <p>
                            <strong>Code Postal : <br/></strong><span>{selectedRow?.store.postal_code}</span>
                        </p>
                        <p>
                            <strong>Ville : <br/></strong><span>{selectedRow?.store.city}</span>
                        </p>
                        <p>
                            <strong>Pays : <br/></strong><span>{selectedRow?.store.country}</span>
                        </p>
                    </div>
                </Card>

            </Modal>


        </>


    );
}

export default GainsTable;