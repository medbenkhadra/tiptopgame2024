import React, {useRef, useState, useEffect} from 'react';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import {Card, Col, message, Modal, Row, Spin} from 'antd';
import Image from 'next/image';
import BarcodeTicketImg from "@/assets/images/barcodeTicket.png";

import {confirmPrintTicket, getTicketByCode, printRandomTicket} from "@/app/api";
import LogoutService from "@/app/service/LogoutService";
import {Button, Form, Input, Select, Space, theme} from 'antd';
import {PrinterOutlined, UndoOutlined} from "@ant-design/icons";
import {
    CheckCircleOutlined,
    ClockCircleOutlined,
    CloseCircleOutlined,
    ExclamationCircleOutlined,
    MinusCircleOutlined,
    SyncOutlined,
} from '@ant-design/icons';
import {Tag } from 'antd';
const {Option} = Select;
import InfuserImg from "@/assets/images/infuser.png";
import TeaBoxImg from "@/assets/images/teaBox.png";
import TeaBoxSignatureImg from "@/assets/images/teaBoxSignature.png";
import SurpriseBoxImg from "@/assets/images/surprise.png";
import SurprisePlusImg from "@/assets/images/surprisePlus.png";

interface DataType {
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
    store: {
      id: string;
        name: string;
        address: string;
        city: string;
        postalCode: string;
        phone: string;
    };
    user : {
        id: string;
        lastname: string;
        firstname: string;
        email: string;
    };
    employee : {
        id: string;
        lastname: string;
        firstname: string;
        email: string;
    };


}

interface SearchParams {
    ticket_code: string;
}

const defaultSearchParams: SearchParams = {
    ticket_code: '',

};

function TicketsPageDashboard() {

    const {logoutAndRedirectAdminsUserToLoginPage} = LogoutService();

    const [data, setData] = useState<DataType[]>();
    const [loading, setLoading] = useState(false);
    const [searchParam, setSearchParam] = useState<SearchParams>(defaultSearchParams);
    const [totalTicketsCount, setTotalTicketsCount] = useState(0);
    const [userRole , setUserRole] = useState<string | null>('');
    const [userStoreId , setUserStoreId] = useState<string>('');
    useEffect(() => {
        setUserRole(localStorage.getItem('loggedInUserRole'));
        const user = JSON.parse(localStorage.getItem('loggedInUser') || '{}');
        setUserStoreId(user['store'][0][0]['id']);
    }, []);




    async function fetchData() {
        setLoading(true);
        if(searchParam.ticket_code.length==0){
            setLoading(false);
            return;
        }
        await getTicketByCode(searchParam.ticket_code).then((response) => {

            setData(response.tickets);
            setTotalTicketsCount(response.totalCount);
            setLoading(false);
        }).catch((err) => {
            setLoading(false);
            if (err.response) {
                if (err.response.status === 401) {
                    logoutAndRedirectAdminsUserToLoginPage();
                }
            } else {
                console.log(err.request);
            }
        })
    }

    useEffect(() => {
        fetchData();
    }, [searchParam ]);


    const getTicketStatusLabel = (status: string) => {
        switch (status) {
            case "1":
                return 'Ticket Généré';
            case "2":
                return 'Ticket Imprimé';
            case "3":
                return 'Ticket en attente de vérification';
            case "4":
                return 'Ticket Gagnant';
            case "5":
                return 'Ticket Expiré';
            case  "6":
                return 'Ticket Annuler';
            default:
                return 'Inconnu';
        }

    }




    const printTicket = (ticketCode: string) => {
        Modal.confirm({
            title: 'Impression de Ticket',
            icon: <PrinterOutlined />,
            content: <>
                <p>Voulez-vous vraiment imprimer ce ticket ?</p>
                <p>Code de Ticket : {ticketCode}</p>
            </>,
            okText: 'Imprimer',
            cancelText: 'Annuler',
            onOk: () => {
                confirmPrintTicket(ticketCode).then(response => {
                        Modal.success({
                            title: 'Impression de Ticket',
                            icon: <PrinterOutlined />,
                            content: (
                                <div>
                                    <p>Le ticket a été imprimé avec succès !</p>
                                    <p>Code de Ticket : {ticketCode}</p>
                                </div>
                            ),
                            onOk: () => {
                                fetchData();
                            }
                        });
                }).catch(err => {
                    if (err.response) {
                        if (err.response.status === 401) {
                            logoutAndRedirectAdminsUserToLoginPage();
                        }
                    } else {
                        Modal.error({
                            title: 'Impression de Ticket',
                            icon: <PrinterOutlined />,
                            content: (
                                <div>
                                    <p>Une erreur est survenue lors de l'impression du ticket !</p>
                                    <p>Code de Ticket : {ticketCode}</p>
                                </div>
                            ),
                        });
                    }
                })
            },
        });
    }

    const renderPrizeImage = (prizeId: string) => {
        switch (prizeId.toString()) {
            case "1":
                return (
                    <Image src={InfuserImg} alt={"Infuseur"}></Image>
                );
            case "2":
                return (
                    <Image src={TeaBoxImg} alt={"Infuseur"}></Image>
                );
            case "3":
                return (
                    <Image src={TeaBoxSignatureImg} alt={"Infuseur"}></Image>
                );
            case "4":
                return (
                    <Image src={SurpriseBoxImg} alt={"Infuseur"}></Image>
                );
            case "5":
                return (
                    <Image src={SurprisePlusImg} alt={"Infuseur"}></Image>
                );
            default:
                return (<></>);
        }
    }


    const renderTickets = () => {
        if (data) {
            return data.map((ticket, key) => {
                return (
                    <Col key={key+'_ticket_list'} className={`w-100 d-flex mt-5 justify-content-center`} xs={24} sm={24} md={24} lg={24} span={24}>
                        <div className={`${styles.oneTicketCardElement}`}>

                            <div className={`${styles.ticketCardBody}`}>
                                <div className={`${styles.ticketCardTextOneTicket} mb-1`}>
                                    <p className={`${styles.ticketStatusTag}
                                     ${ticket.status=="1" && styles.ticketStatusTagGenerated}
                                        ${ticket.status=="2" && styles.ticketStatusTagPrinted}
                                        ${ticket.status=="3" && styles.ticketStatusTagWaiting}
                                        ${ticket.status=="4" && styles.ticketStatusTagWin}
                                        ${ticket.status=="5" && styles.ticketStatusTagExpired}
                                        ${ticket.status=="6" && styles.ticketStatusTagCanceled}
                                     `}>
                                        {ticket.status=="1" && (
                                            <Tag icon={<CheckCircleOutlined />} color="success">
                                                {getTicketStatusLabel(ticket.status.toString())}
                                            </Tag>

                                        )}
                                        {ticket.status=="2" && (
                                            <Tag icon={<PrinterOutlined />} color="success">
                                                {getTicketStatusLabel(ticket.status.toString())}
                                            </Tag>

                                        )}
                                        {ticket.status=="3" && (
                                            <Tag icon={ <ClockCircleOutlined />} color="error">
                                                {getTicketStatusLabel(ticket.status.toString())}
                                            </Tag>

                                        )}
                                        {ticket.status=="4" && (
                                            <Tag icon={<CheckCircleOutlined />} color="default">
                                                {getTicketStatusLabel(ticket.status.toString())}
                                            </Tag>

                                        )}
                                        {ticket.status=="5" && (
                                            <Tag icon={<CheckCircleOutlined />} color="default">
                                                {getTicketStatusLabel(ticket.status.toString())}
                                            </Tag>

                                        )}


                                    </p>


                                    <p className={`${styles.barCode}`}><strong>Code de Ticket:</strong> <span className={styles.barCodeText}>#{ticket.ticket_code} <div className={`${styles.ticketCardIconsBarCode}`}>
                                        <Image src={BarcodeTicketImg} alt={"Code a barre"}></Image>
                                    </div>
                                    </span></p>


                                    {ticket.status=="4" && (
                                    <>
                                        <p><strong>Gain:</strong></p>
                                        <div className={`${styles.ticketCardIconsPrize}`}>
                                            {renderPrizeImage(ticket.prize.id)}
                                        </div>
                                        <p className={`${styles.prizeLabel}`}>{ticket.prize.label}</p>

                                    </>
                                        )}

                                    {ticket.status=="1" && (
                                        <p className={`mt-5 ${styles.prizeDatesTextAux}`}><strong>Date de Génération:</strong>Le {ticket.ticket_generated_at.date} à {ticket.ticket_generated_at.time} </p>
                                    )}
                                    {ticket.status=="2" && (
                                        <p className={`mt-5 ${styles.prizeDatesTextAux}`}><strong>Date d'impression:</strong>Le {ticket.ticket_printed_at.date} à {ticket.ticket_printed_at.time} </p>
                                    )}
                                    {ticket.status=="3" && (
                                        <>
                                            <p className={`mt-5 ${styles.prizeDatesTextAux}`}><strong>Date d'impression:</strong>Le {ticket.ticket_printed_at.date} à {ticket.ticket_printed_at.time} </p>
                                            <p className={`mt-2 ${styles.prizeDatesTextAux}`}><strong>Caissier:</strong> {ticket.employee.lastname} {ticket.employee.firstname}  </p>
                                            <p className={`mt-2 ${styles.prizeDatesTextAux}`}><strong>Caissier ID:</strong> #{ticket.employee.id} </p>

                                            <p className={`mt-2 ${styles.prizeDatesTextAux}`}><strong>Date de jeu:</strong>Le {ticket.win_date.date} à {ticket.win_date.time} </p>
                                            <p className={`mt-2 ${styles.prizeDatesTextAux}`}><strong>Client:</strong> {ticket.user.lastname} {ticket.user.firstname}  </p>
                                            <p className={`mt-2 ${styles.prizeDatesTextAux}`}><strong>Participant E-mail:</strong> {ticket.user.email} </p>
                                            <p className={`mt-2 ${styles.prizeDatesTextAux}`}><strong>Participant ID:</strong> #{ticket.user.id} </p>


                                        </>
                                    )}
                                    {ticket.status=="4" && (
                                        <>
                                            <p className={`mt-5 ${styles.prizeDatesTextAux}`}><strong>Date d'impression:</strong>Le {ticket.ticket_printed_at.date} à {ticket.ticket_printed_at.time} </p>
                                            <p className={`mt-2 ${styles.prizeDatesTextAux}`}><strong>Caissier:</strong> {ticket.employee.lastname} {ticket.employee.firstname}  </p>
                                            <p className={`mt-2 ${styles.prizeDatesTextAux}`}><strong>Caissier ID:</strong> #{ticket.employee.id} </p>
                                            <p className={`mt-2 ${styles.prizeDatesTextAux}`}><strong>Date de Gain:</strong>Le {ticket.win_date.date} à {ticket.win_date.time} </p>
                                            <p className={`mt-2 ${styles.prizeDatesTextAux}`}><strong>Participant:</strong> {ticket.user.lastname} {ticket.user.firstname}  </p>
                                            <p className={`mt-2 ${styles.prizeDatesTextAux}`}><strong>Participant E-mail:</strong> {ticket.user.email} </p>
                                            <p className={`mt-2 ${styles.prizeDatesTextAux}`}><strong>Participant ID:</strong> #{ticket.user.id} </p>


                                        </>
                                    )}

                                    {ticket.status=="1" && (
                                        <Button onClick={() => {
                                            printTicket(ticket.ticket_code);
                                        }} className={`${styles.printBtn} mt-3`}  title={`Plus de détails`} icon={<PrinterOutlined />} size={"large"} >
                                            Imprimer le ticket
                                        </Button>
                                    )}


                                </div>
                            </div>

                        </div>
                    </Col>

                )
            })
        }
    }


    const {token} = theme.useToken();
    const [form] = Form.useForm();

    const formStyle: React.CSSProperties = {
        maxWidth: 'none',
        background: token.colorFillAlter,
        borderRadius: token.borderRadiusLG,
        padding: 24,
    };



    const getFields = () => {
        const children = [];
        children.push(
            <Row className={`${styles.fullWidthElement} w-100 d-flex`} gutter={24}>
                <Col span={24} key={`barCodeInput`}>
                    <Form.Item
                        className={`${styles.formItem} searchTicketFormItem mb-5 pb-2`}
                        name={`code`}
                        label={`Entrer le code à barre du ticket manuellement`}
                    >
                        <Input className={`mt-2`} placeholder="Code à barre de Ticket"
                               onChange={(e) => {
                                   setSearchParam({
                                       ...searchParam,
                                       ticket_code: e.target.value,
                                   });
                               }}
                        />
                    </Form.Item>
                </Col>


            </Row>,
        );

        return children;
    };

    function handlePrintAutoTicket() {
        Modal.confirm({
            title: 'Impression de Ticket',
            icon: <PrinterOutlined />,
            content: <>
                <p>Voulez-vous vraiment imprimer un ticket aléatoire ?</p>
            </>,
            okText: 'Imprimer',
            cancelText: 'Annuler',
            onOk: () => {
                printRandomTicket().then(response => {
                    Modal.success({
                        title: 'Impression de Ticket',
                        icon: <PrinterOutlined />,
                        content: (
                            <div>
                                <p>Le ticket a été imprimé avec succès !</p>
                                <p>Code de Ticket : {response.ticket.ticket_code}</p>
                            </div>
                        ),
                        onOk: () => {
                            setSearchParam({
                                ...searchParam,
                                ticket_code: response.ticket.ticket_code,
                            });
                        }
                    });
                }).catch(err => {
                    if (err.response) {
                        if (err.response.status === 401) {
                            logoutAndRedirectAdminsUserToLoginPage();
                        }else {
                            Modal.error({
                                title: 'Impression de Ticket',
                                icon: <PrinterOutlined />,
                                okText: 'D\'accord',
                                content: (
                                    <div>
                                        <p>Une erreur est survenue lors de l'impression du ticket !</p>
                                        <p>
                                            Veuillez contactez l'administrateur du système
                                        </p>
                                    </div>
                                ),
                            });
                        }
                    } else {
                        Modal.error({
                            title: 'Impression de Ticket',
                            icon: <PrinterOutlined />,
                            content: (
                                <div>
                                    <p>Une erreur est survenue lors de l'impression du ticket !</p>
                                </div>
                            ),
                        });
                    }
                })
            },
        });
    }

    const renderSearchForm = () => {
        return (
            <>
                <Form form={form} name="advanced_search" className={`${styles.searchOneTicketForm}`}>
                    <Row className={`${styles.fullWidthElement}`} gutter={24}>{getFields()}</Row>
                    <Row className={`${styles.fullWidthElement}`} gutter={24}>
                        <strong>
                            Ou générer un ticket aléatoire pour impression
                        </strong>
                    </Row>


                    <div className={`mt-0 w-100`} style={{textAlign: 'right'}}>
                        <Space size="small" className={`w-100 d-flex justify-content-between`}>
                            <Button
                                className={`${styles.greenButton}`}

                                onClick={() => {
                                    form.resetFields();
                                    handlePrintAutoTicket();
                                }}
                            >
                                Impression automatique <PrinterOutlined />
                            </Button>

                            <Button
                                className={`${styles.submitButtonBlue}`}

                                onClick={() => {
                                    form.resetFields();
                                    setSearchParam(defaultSearchParams);
                                    setData(undefined);
                                }}
                            >
                                <UndoOutlined />
                            </Button>

                        </Space>
                    </div>
                </Form>
            </>
        );

    }


    return (
        <div className={styles.homePageContent}>

            <div className={`${styles.homePageContentTopHeader}`}>
                <h1 className={`mx-3`}>
                    Impression de Ticket
                </h1>
                <div className={`${styles.ticketsCardsMain}`}>

                    <div className={`${styles.ticketsCardsDiv} mb-5 px-4`}>
                        {renderSearchForm()}

                        <Row className={`${styles.fullWidthElement}  mt-5 mb-5 w-100`}
                             gutter={{xs: 8, sm: 16, md: 24, lg: 32}}>

                            {loading &&
                                <div className={`${styles.loadingDashboardFullPage}`}>
                                    <Spin size="large"/>
                                </div>
                            }
                            {!loading && (
                               <>


                                       <div className={`w-100 justify-content-center`}>
                                           {renderTickets()}
                                       </div>


                                   {totalTicketsCount==0 && (
                                        <Col key={"noResultTikcets"} className={`w-100 d-flex justify-content-between mt-3 px-4`} xs={24} sm={24} md={24} lg={24} span={6}>

                                        </Col>

                                   )}
                               </>
                               )}

                        </Row>


                    </div>

                </div>
            </div>


        </div>
    );
}

export default TicketsPageDashboard;