import React, {useRef, useState, useEffect} from 'react';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import {Col, Modal, Row, Spin} from 'antd';
import Image from 'next/image';
import BarcodeTicketImg from "@/assets/images/barcodeTicket.png";
import InfuserImg from "@/assets/images/infuser.png";
import TeaBoxImg from "@/assets/images/teaBox.png";
import TeaBoxSignatureImg from "@/assets/images/teaBoxSignature.png";
import SurprisePlusImg from "@/assets/images/surprisePlus.png";
import SurpriseBoxImg from "@/assets/images/surprise.png";
import BoxImg from "@/assets/images/box.png";
import {getTickets , getPrizes} from "@/app/api";
import LogoutService from "@/app/service/LogoutService";
import {Button, Form, Input, Select, Space, theme} from 'antd';
import {DownOutlined, EyeOutlined, PrinterOutlined} from "@ant-design/icons";
import {
    CheckCircleOutlined,
    ClockCircleOutlined,
    CloseCircleOutlined
} from '@ant-design/icons';
import {Tag } from 'antd';
const {Option} = Select;
import {Pagination} from 'antd';
import StoresList from "@/app/components/dashboardComponents/TicketsPageComponent/components/StoresList";

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
    updated_at: {
        date: string;
        time: string;
    };
    ticket_generated_at: {
        date: string;
        time: string;
    };
    client: string;
    caissier: string;
    store: {
        id: string;
        name: string;
    };
    employee: {
        id: string;
        firstname: string;
        lastname: string;
        email: string;
    };
    user: {
        id: string;
        firstname: string;
        lastname: string;
        email: string;
    };


}

interface PrizeType {
    'id' : string;
    'label' : string;
    'name' : string;
    'type' : string;
    'prize_value' : string;
    'winning_rate' : string;
}


interface SearchParams {
    page: string;
    limit: string;
    store: string;
    user: string;
    status: string;
    caissier: string;
    client: string;
    sort: string;
    order: string;
    ticket_code: string;
    prize: string;
}

const defaultSearchParams: SearchParams = {
    page: '1',
    limit: '12',
    store: '',
    user: '',
    status: '',
    caissier: '',
    client: '',
    sort: '',
    order: '',
    ticket_code: '',
    prize: '',
};

function TicketsPageDashboard() {

    const {logoutAndRedirectAdminsUserToLoginPage} = LogoutService();

    const [data, setData] = useState<DataType[]>();
    const [loading, setLoading] = useState(false);
    const [searchParam, setSearchParam] = useState<SearchParams>(defaultSearchParams);
    const [totalTicketsCount, setTotalTicketsCount] = useState(0);
    const [userRole , setUserRole] = useState<string | null>('');
    useEffect(() => {
        setUserRole(localStorage.getItem('loggedInUserRole'));
    }, []);

    async function fetchData() {
        setLoading(true);
        await getTickets(searchParam).then((response) => {
            console.log('response : ', response);
            setData(response.tickets);
            setTotalTicketsCount(response.totalCount);
            setLoading(false);
        }).catch((err) => {
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
    }, [searchParam]);

    const getTicketStatusLabel = (status: string) => {
        console.log('status : ', status=="1");
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

    const renderPrizeImage = (prizeId: string , status : string) => {
        if(prizeId==null) {
            return (<></>);
        }

        if ((status=="1" || status=="2") && userRole!="ROLE_ADMIN") {
            return (
                <Image src={BoxImg} alt={"BoxImg"}></Image>
            );
        }

        console.log('prizeId : ', prizeId.toString());


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

    const [isDetailsModalOpen, setIsDetailsModalOpen] = useState(false);
    const [selectedTicket, setSelectedTicket] = useState<DataType>();

    const openDetailsModal = (ticket: DataType) => {
        setSelectedTicket(ticket);
        setIsDetailsModalOpen(true);
    }

    const renderTickets = () => {
        if (data) {
            return data.map((ticket, key) => {
                return (
                    <Col key={key+'_ticket_list'} className={`w-100 d-flex mt-5`} xs={24} sm={24} md={8} lg={6} span={6}>
                        <div className={`${styles.ticketCardElement}`}>

                            <div className={`${styles.ticketCardBody}`}>
                                <div className={`${styles.ticketCardText} mb-1`}>
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
                                            <Tag icon={<PrinterOutlined/>} color="success">
                                                {getTicketStatusLabel(ticket.status.toString())}
                                            </Tag>

                                        )}
                                        {ticket.status=="3" && (
                                            <Tag icon={<ClockCircleOutlined />} color="success">
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
                                        {ticket.status=="6" && (
                                            <Tag icon={
                                                <CloseCircleOutlined />
                                            } color="error">
                                                {getTicketStatusLabel(ticket.status.toString())}
                                            </Tag>

                                        )}


                                    </p>

                                    <p className={`${styles.barCode}`}><strong>Code de Ticket:</strong> <span className={styles.barCodeText}>#{ticket.ticket_code} <div className={`${styles.ticketCardIconsBarCode}`}>
                                        <Image src={BarcodeTicketImg} alt={"Code a barre"}></Image>
                                    </div>
                                    </span></p>



                                    {(userRole === 'ROLE_ADMIN'|| ticket.status=="1" || ticket.status=="4" || ticket.status=="6" || ticket.status=="5" || ticket.status=="2" || ticket.status=="3" ) &&  (
                                        <>
                                            <p><strong>Gain:</strong></p>
                                            <div className={`${styles.ticketCardIconsPrize}`}>
                                                {renderPrizeImage(ticket.prize.id , ticket.status)}
                                            </div>
                                            {(ticket.status=="4" || ticket.status=="3" || ticket.status=="5" || ticket.status=="6") && (
                                                <p className={`${styles.prizeLabel}`}>{ticket.prize.label}</p>
                                            )}
                                        </>
                                    )}



                                    {(ticket.status=="1" )&& (
                                        <p className={`mt-2 ${styles.prizeDateGeneration}`}><strong>Date de Génération:</strong>Le {ticket.ticket_generated_at.date} à {ticket.ticket_generated_at.time} </p>
                                    )}
                                    {(ticket.status!="1") && (
                                        <>
                                             <p className={`mt-2 ${styles.prizeDateGeneration}`}><strong>Date d'impression:</strong>Le {ticket.ticket_printed_at.date} à {ticket.ticket_printed_at.time} </p>
                                        </>
                                         )}
                                    {ticket.status=="3" && (
                                        <p className={`mt-2 ${styles.prizeDateGeneration}`}><strong>Date de jeu:</strong>Le {ticket.updated_at.date} à {ticket.updated_at.time} </p>
                                    )}
                                    {ticket.status=="4" && (
                                        <p className={`mt-2 ${styles.prizeDateGeneration}`}><strong>Date de Gain:</strong>Le {ticket.win_date.date} à {ticket.win_date.time} </p>
                                    )}

                                    {ticket.status=="3" && (userRole=="ROLE_ADMIN" || userRole=="ROLE_STOREMANAGER") &&
                                        (
                                            <a
                                                onClick={() => {

                                                }}
                                                className={`${styles.cancelTicketBtn} mt-3`}  title={`Annuler le ticket`}  >
                                                Annuler le ticket <CloseCircleOutlined />
                                            </a>

                                        )}


                                    {ticket.status=="5" && (
                                        <p className={`mt-2 ${styles.prizeDateGeneration}`}><strong>Date d'expiration:</strong>Le {ticket.updated_at.date} à {ticket.updated_at.time} </p>
                                    )}

                                    {ticket.status=="6" && (
                                        <p className={`mt-2 ${styles.prizeDateGeneration}`}><strong>Date d'annulation:</strong>Le {ticket.updated_at.date} à {ticket.updated_at.time} </p>
                                    )}

                                    <Button onClick={()=> {
                                        openDetailsModal(ticket);
                                    }} className={`${styles.eyeIcon} mt-3`}  title={`Plus de détails`} icon={<EyeOutlined />} size={"large"} >
                                    Consulter
                                    </Button>




                                </div>
                            </div>

                        </div>
                    </Col>

                )
            })
        }
    }

    const [selectedStoreId, setSelectedStoreId] = useState<string>('');

    const handleStoreChange = (value: string) => {
        setSelectedStoreId(value);
        setSearchParam({
            ...searchParam,
            store: value,
        });
    };

    const {token} = theme.useToken();
    const [form] = Form.useForm();
    const [expand, setExpand] = useState(false);

    const formStyle: React.CSSProperties = {
        maxWidth: 'none',
        background: token.colorFillAlter,
        borderRadius: token.borderRadiusLG,
        padding: 24,
    };



    const renderStores = () => {
        return (
                <StoresList onSelectStore={handleStoreChange}></StoresList>
        )
    }

    const renderTicketsStatus = () => {
        return (
            <>
                <Option value="">
                    Tous les Statuts
                </Option>
                {userRole === 'ROLE_ADMIN' && (
                    <>
                        <Option  value="1">
                            Ticket Généré
                        </Option>
                        <Option value="2">Ticket Imprimé</Option>
                    </>
                )}
                {userRole==="ROLE_EMPLOYEE" && (
                    <>
                        <Option value="2">Ticket Imprimé</Option>
                    </>
                )}
                <Option value="3">Ticket en attente de vérification</Option>
                <Option value="4">Ticket Gagnant</Option>
                <Option value="5">Ticket Expiré</Option>
                <Option value="6">Ticket Annuler</Option>
            </>
        )
    }

    const [prizesList, setPrizesList] = useState<PrizeType[]>([]);
    function getAllPrizes() {
        setLoading(true);
        getPrizes().then((response) => {
            console.log('response : ', response);
            setPrizesList(response.prizes);
        }).catch((err) => {
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
        getAllPrizes();
    },[]);

    const renderTicketsPrizes = () => {
        return (
            <>
                <Option value="">
                    Tous les Gains
                </Option>
                {prizesList.map((prize, key) => {
                    return (
                        <Option key={key+'_prize_list'} value={prize.id}>{prize.label}</Option>
                    )
                })}

            </>
        )
    }

    const getFields = () => {
        const count = expand ? 10 : 6;
        const children = [];
        children.push(
            <Row className={`${styles.fullWidthElement} w-100 d-flex`} gutter={24}>
                <Col span={8} key={`barCodeInput`}>
                    <Form.Item
                        className={`${styles.formItem} searchTicketFormItem mb-5`}
                        name={`code`}
                        label={`Code de Ticket:`}
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

                    {userRole === 'ROLE_ADMIN' || userRole === 'ROLE_STOREMANAGER' && (
                        <>
                        <Form.Item
                        className={`${styles.formItem} searchTicketFormItem mb-5`}
                        name={`client`}
                        label={`Client`}
                        initialValue=""
                    >
                        <Input className={`mt-2`}
                               placeholder="Nom, Prénom, de Client"
                               onChange={(e) => {
                                   setSearchParam({
                                       ...searchParam,
                                       client: e.target.value,
                                   });
                               }}
                        />
                    </Form.Item>
                        </>
                        )}

                </Col>
                <Col span={8} key={`statusTicketInput`}>


                    <Form.Item
                        className={`${styles.formItem} searchTicketFormItem mb-5`}
                        name={`status`}
                        label={`Statut de Ticket`}
                        initialValue=""
                    >

                        <Select placeholder={`Tous les Statuts`} value={searchParam.status} onChange={(e) => {
                            setSearchParam({
                                ...searchParam,
                                status: e.toString(),
                            });
                        }} className={`mt-2`}>
                            {renderTicketsStatus()}
                        </Select>
                    </Form.Item>

                    {userRole === 'ROLE_ADMIN' || userRole === 'ROLE_STOREMANAGER' && (
                        <>
                            <Form.Item
                                className={`${styles.formItem} searchTicketFormItem mb-5`}

                                name={`caissier`}
                                label={`Caissier`}
                                initialValue=""
                            >
                                <Input
                                    className={`mt-2`}
                                    placeholder="Nom, Prénom, de Caissier"
                                    onChange={(e) => {
                                        setSearchParam({
                                            ...searchParam,
                                            caissier: e.target.value,
                                        });
                                    }}
                                />
                            </Form.Item>
                        </>
                    )}

                </Col>

                <Col span={8} key={`storesTicketsList`}>
                    {userRole === 'ROLE_ADMIN' && (
                    <>
                        {renderStores()}
                    </>
                        )}



                    {expand && (
                        <>
                            <Form.Item
                                name={`gain`}
                                label={`Gain`}
                                initialValue=""
                                className={`${styles.formItem} searchTicketFormItem mb-5`}
                            >
                                <Select onChange={
                                    (e) => {
                                        setSearchParam({
                                            ...searchParam,
                                            prize: e.toString(),
                                        });
                                    }
                                } placeholder={`Tous les Gains`
                                } className={`mt-2`}>
                                    {renderTicketsPrizes()}
                                </Select>
                            </Form.Item>
                        </>
                    )}
                </Col>


            </Row>,
        );

        return children;
    };

    const renderSearchForm = () => {
        return (
            <>
                <Form form={form} name="advanced_search" className={`${styles.searchTicketForm}`}>
                    <Row className={`${styles.fullWidthElement}`} gutter={24}>{getFields()}</Row>
                    <div className={`mt-0 w-100`} style={{textAlign: 'right'}}>
                        <Space size="small">
                            <Button
                                className={`${styles.submitButtonBlue}`}

                                onClick={() => {
                                    form.resetFields();
                                    setSearchParam(defaultSearchParams);
                                }}
                            >
                                Réinitialiser
                            </Button>
                            {userRole === 'ROLE_ADMIN' && (
                            <a
                                className={`${styles.moreFiltersBtn} ${expand ? styles.moreFiltersBtnActive : styles.moreFiltersBtnInactive}`}
                                style={{fontSize: 12}}
                                onClick={() => {
                                    setExpand(!expand);
                                }}
                            >
                                <DownOutlined
                                    rotate={expand ? 180 : 0}/> {!expand ? 'Plus de filtres' : 'Moins de filtres'}
                            </a>
                                )}
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
                    {userRole === 'ROLE_ADMIN' && (
                        <>
                            Tickets et codes générés
                        </>
                        )}
                    {userRole === 'ROLE_STOREMANAGER' && (
                        <>
                            Tickets associés au magasin
                        </>
                    )}

                    {userRole === 'ROLE_CLIENT' && (
                        <>
                            Tickets associés au client
                        </>
                    )}
                    {userRole === 'ROLE_EMPLOYEE' && (
                        <>
                            Tickets associés au caissier
                        </>
                    )}


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
                                   <Col key={"resultTikcets"} className={`w-100 d-flex justify-content-between mt-3 px-4`} xs={24} sm={24} md={24} lg={24} span={6}>
                                    <h6>
                                        Résultat de recherche
                                    </h6>
                                       <h6>
                                           {data?.length} Ticket(s) trouvé(s) sur {totalTicketsCount}
                                       </h6>

                                   </Col>
                                   {renderTickets()}

                                   {totalTicketsCount==0 && (
                                        <Col key={"noResultTikcets"} className={`w-100 d-flex justify-content-between mt-3 px-4`} xs={24} sm={24} md={24} lg={24} span={6}>
                                             <h6>
                                                  Aucun ticket trouvé !
                                             </h6>
                                        </Col>

                                   )}
                               </>
                               )}
                        </Row>
                        {!loading && totalTicketsCount>0 &&
                            <Row className={`${styles.fullWidthElement} w-100 mt-5 justify-content-center`}
                                 gutter={{xs: 8, sm: 16, md: 24, lg: 32}}>

                                <Pagination
                                    onChange={(page, pageSize) => {
                                        setSearchParam({
                                            ...searchParam,
                                            page: page.toString(),
                                            limit: pageSize.toString(),
                                        });
                                    }
                                    }
                                    defaultCurrent={parseInt(searchParam.page)} total={totalTicketsCount}/>
                            </Row>
                        }


                    </div>

                </div>
            </div>

            <Modal
                title={
                <>
                <h3 className={`mt-5`}>
                    Détails de Ticket
                </h3>
                </>
                }
                open={isDetailsModalOpen}
                onOk={() => {
                    setIsDetailsModalOpen(false);
                }}
                onCancel={() => {
                    setIsDetailsModalOpen(false);
                }}
                footer={[
                    <Button key="backBtn" onClick={() => {
                        setIsDetailsModalOpen(false);
                    }}>
                        Fermer
                    </Button>,
                ]}
            >
                <div className={`${styles.ticketDetailsModal}`}>
                    <div className={`${styles.ticketDetailsModalLeft}`}>
                        <div className={`${styles.ticketDetailsModalLeftHeader}`}>
                            <p className={`${styles.ticketStatusTag}
                                     ${selectedTicket?.status=="1" && styles.ticketStatusTagGenerated}
                                        ${selectedTicket?.status=="2" && styles.ticketStatusTagPrinted}
                                        ${selectedTicket?.status=="3" && styles.ticketStatusTagWaiting}
                                        ${selectedTicket?.status=="4" && styles.ticketStatusTagWin}
                                        ${selectedTicket?.status=="5" && styles.ticketStatusTagExpired}
                                        ${selectedTicket?.status=="6" && styles.ticketStatusTagCanceled}
                                     `}>
                                {selectedTicket?.status=="1" && (
                                    <Tag icon={<CheckCircleOutlined />} color="success">
                                        {getTicketStatusLabel(selectedTicket?.status.toString())}
                                    </Tag>

                                )}
                                {selectedTicket?.status=="2" && (
                                    <Tag icon={<PrinterOutlined/>} color="success">
                                        {getTicketStatusLabel(selectedTicket?.status.toString())}
                                    </Tag>

                                )}
                                {selectedTicket?.status=="3" && (
                                    <Tag icon={<ClockCircleOutlined />} color="success">
                                        {getTicketStatusLabel(selectedTicket?.status.toString())}
                                    </Tag>

                                )}
                                {selectedTicket?.status=="4" && (
                                    <Tag icon={<CheckCircleOutlined />} color="default">
                                        {getTicketStatusLabel(selectedTicket?.status.toString())}
                                    </Tag>

                                )}
                                {selectedTicket?.status=="5" && (
                                    <Tag icon={<CheckCircleOutlined />} color="default">
                                        {getTicketStatusLabel(selectedTicket?.status.toString())
                                        }
                                    </Tag>
                                )}

                                {selectedTicket?.status=="6" && (
                                    <Tag icon={
                                        <CloseCircleOutlined />
                                    } color="error">
                                        {getTicketStatusLabel(selectedTicket?.status.toString())}
                                    </Tag>

                                )}

                            </p>
                            <p className={`${styles.barCode}`}><strong>Code de Ticket:</strong> <span className={styles.barCodeText}>#{selectedTicket?.ticket_code} <div className={`${styles.ticketCardIconsBarCode}`}>
                                        <Image src={BarcodeTicketImg} alt={"Code a barre"}></Image>
                                    </div>
                                    </span>
                            </p>

                            {(selectedTicket?.status!="1") && (<>
                                <p>
                                    <strong>Date d'impression : <br/>
                                    </strong>
                                    <span>
                                    {selectedTicket?.ticket_printed_at.date} à {selectedTicket?.ticket_printed_at.time}
                                </span>
                                </p>
                                    {(selectedTicket?.status=="3" || selectedTicket?.status=="4") && (
                                        <p>
                                            <strong>Participant : <br/>
                                            </strong>
                                            <span>
                                                {selectedTicket?.user.firstname} {selectedTicket?.user.lastname}
                                            </span>
                                            <br/>
                                            <span>
                                                {selectedTicket?.user.email}
                                            </span>
                                        </p>
                                    )}
                                <p>
                                    <strong>Caissier : <br/>
                                    </strong>
                                    <span>
                                    {selectedTicket?.employee.firstname} {selectedTicket?.employee.lastname}
                                </span>

                                    <br/>
                                    <span>
                                    {selectedTicket?.employee.email}
                                </span>

                                </p>

                                    <p>
                                        <strong>Magasin : <br/>
                                        </strong>
                                        <span>
                                    {selectedTicket?.store.name}
                                </span>
                                    </p>

                                </>
                            )}




                            {(userRole === 'ROLE_ADMIN' || selectedTicket?.status=="1" || selectedTicket?.status=="4" || selectedTicket?.status=="6" || selectedTicket?.status=="5" || selectedTicket?.status=="2" || selectedTicket?.status=="3" ) &&  (
                                <>
                                    <p><strong>Gain:</strong></p>
                                    <div className={`${styles.ticketCardIconsPrize}`}>
                                        {renderPrizeImage(selectedTicket?.prize.id as string , selectedTicket?.status as string)}
                                    </div>
                                    {(selectedTicket?.status=="4" || selectedTicket?.status=="3" || selectedTicket?.status=="5" || selectedTicket?.status=="6") && (
                                        <p className={`${styles.prizeLabel}`}>{selectedTicket?.prize.label}</p>
                                    )}

                                </>
                            )}



                            {selectedTicket?.status=="1" && (
                                <p className={`mt-5 ${styles.prizeDateGeneration}`}><strong>Date de Génération:</strong>Le {selectedTicket?.ticket_generated_at.date} à {selectedTicket?.ticket_generated_at.time} </p>
                            )}
                            {selectedTicket?.status!="1" && (
                                <>
                                    <p className={`mt-5 ${styles.prizeDateGeneration}`}><strong>Date d'impression:</strong>Le {selectedTicket?.ticket_printed_at.date} à {selectedTicket?.ticket_printed_at.time} </p>
                                </>
                            )}
                            {selectedTicket?.status=="3" && (
                                <p className={`mt-1 ${styles.prizeDateGeneration}`}><strong>Date de jeu:</strong>Le {selectedTicket?.updated_at.date} à {selectedTicket?.updated_at.time} </p>
                            )}
                            {selectedTicket?.status=="4" && (
                                <p className={`mt-1 ${styles.prizeDateGeneration}`}><strong>Date de Gain:</strong>Le {selectedTicket?.win_date.date} à {selectedTicket?.win_date.time} </p>
                            )}

                            {selectedTicket?.status=="3" && (userRole=="ROLE_ADMIN" || userRole=="ROLE_STOREMANAGER") &&
                                (
                                    <a
                                        onClick={() => {

                                        }}
                                        className={`${styles.cancelTicketBtn} mt-3`}  title={`Annuler le ticket`}  >
                                        Annuler le ticket <CloseCircleOutlined />
                                    </a>

                                )}


                            {selectedTicket?.status=="6" && (
                                <p className={`mt-1 ${styles.prizeDateGeneration}`}><strong>Date d'annulation:</strong>Le {selectedTicket?.updated_at.date} à {selectedTicket?.updated_at.time} </p>
                            )}


                        </div>
                    </div>
                </div>
            </Modal>

        </div>
    );
}

export default TicketsPageDashboard;