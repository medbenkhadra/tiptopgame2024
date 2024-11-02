import React, {useEffect, useState} from 'react';

import {
    Button,
    Col,
    ConfigProvider,
    DatePicker,
    DatePickerProps,
    Form,
    Input,
    Pagination,
    Row,
    Select,
    Space,
    Spin
} from "antd";
import styles from '@/styles/pages/dashboards/storeAdminDashboard.module.css';
import {getAllStoresClientsList, getStoresEmployees, getStoresForAdmin, getTicketsHistory} from "@/app/api";
import LogoutService from "@/app/service/LogoutService";
import locale from "antd/locale/fr_FR";
import {DataType} from "csstype";
import HistoryTable from "@/app/components/dashboardComponents/TicketsHistory/components/HistoryTable";
import {LoadingOutlined} from "@ant-design/icons";

const {Option} = Select;

interface DataType {
    id: string;
    status: string;
    client: {
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

}


interface SearchParams {
    page: string;
    limit: string;
    store: string;
    client: string;
    employee: string;
    sort: string;
    order: string;
    start_date?: string;
    end_date?: string;
    role?: string;
    status?: string;
    ticket_code?: string;
}

const defaultSearchParams: SearchParams = {
    page: '1',
    limit: '12',
    store: '',
    client: '',
    employee: '',
    sort: '',
    order: '',
    start_date: '',
    end_date: '',
    role: '',
    status: '',
    ticket_code: '',
};

const dateFormat = 'DD/MM/YYYY';


function TicketHistory() {
    const {logoutAndRedirectAdminsUserToLoginPage} = LogoutService();
    const [form] = Form.useForm();
    const { RangePicker } = DatePicker;
    const [userRole, setUserRole] = useState<string>('');
    const [loading, setLoading] = useState<boolean>(false);
    const [searchParam, setSearchParam] = useState<SearchParams>(defaultSearchParams);

    const [inputLoading, setInputLoading] = useState<boolean>(false);


    useEffect(() => {
        const userRoleSaved = localStorage.getItem("loggedInUserRole");
        if (userRoleSaved) {
            setUserRole(userRoleSaved);
        }
    }, []);

    const [storesList, setStoresList] = useState<any[]>([]);

    useEffect(() => {
        getAllStores();
    },[]);

    function getAllStores() {
        setLoading(true);
        getStoresForAdmin().then((response) => {
            console.log('response : ', response);
            setStoresList(response.storesResponse);
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


    const [ticketHistoryList , setTicketHistoryList] = useState<DataType[]>([]);
    const [ticketHistoryListCount , setTicketHistoryListCount] = useState<number>(0);

    function getTicketHistory() {
        setLoading(true);
        getTicketsHistory(searchParam).then((response) => {
            console.log('response : ', response);
            setTicketHistoryList(response.ticketHistory);
            setTicketHistoryListCount(response.totalCount);
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
        getTicketHistory();
    }, [searchParam]);







    const handleDateChange: DatePickerProps['onChange'] = (date, dateString) => {
        console.log(date, dateString);
        if (dateString && date) {
            setSearchParam({
                ...searchParam,
                start_date: dateString[0],
                end_date: dateString[1],
            });
        }
    };



    const renderStores = () => {
        return (
            <>
                <Option value="">
                    Tous les Magasins
                </Option>
                {storesList.map((store, key) => {
                    return (
                        <Option key={key} value={store.id}>{store.name}</Option>
                    )
                })}
            </>
        )
    }


    const filterOption = (input: string, item: any) => (item?.label ?? '').toLowerCase().includes(input.toLowerCase());

    const onChangeClientList = (value: string) => {
        setSearchParam({
            ...searchParam,
            client: value,
        });
    };


    const [storesClientsList, setStoresClientsList] = useState<any[]>([]);

    function getAllStoresClients() {
        setLoading(true);
        setInputLoading(true)
        getAllStoresClientsList(searchParam).then((response) => {
            setStoresClientsList(response.users);
            setInputLoading(false)
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
        getAllStoresClients();
        //form.resetFields(['client']);

    },[searchParam.store , searchParam.employee]);

    const renderStoresClients = () => {
        return (
            <>
                <Option value="">
                    Tous les Clients
                </Option>
                {storesClientsList.map((client, key) => {
                    return (
                        <Option key={key} value={client.id}   label={`${client.lastname} ${client.firstname}`} >{client.lastname} {client.firstname}</Option>
                    )
                })}
            </>
        )
    }

    const onChangeEmployeeList = (value: string) => {
        setSearchParam({
            ...searchParam,
            employee: value,
        });
    };


    const [storesEmployeesList, setStoresEmployeesList] = useState<any[]>([]);
    function getAllStoresEmployees() {
        setLoading(true);
        setInputLoading(true)
        getStoresEmployees(searchParam).then((response) => {
            console.log('response : ', response);
            setStoresEmployeesList(response.users);
            setInputLoading(false)
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
        getAllStoresEmployees();
        //form.resetFields(['employee']);
    },[searchParam.store , searchParam.client]);

    function renderStoresEmployees() {
        return (
            <>
                <Option value="">
                    Tous les Caissiers
                </Option>
                {storesEmployeesList.map((employee, key) => {
                    return (
                        <Option key={key} value={employee.id} label={`${employee.lastname} ${employee.firstname}`} >{employee.lastname} {employee.firstname}</Option>
                    )
                })}
            </>
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
                        <Option value="2">
                            Impression de Ticket
                        </Option>
                    </>
                )}
                {userRole==="ROLE_EMPLOYEE" && (
                    <>
                        <Option value="2">
                            Impression de Ticket
                        </Option>
                    </>
                )}
                <Option value="3">
                    Utilisation de Ticket (Jouer)
                </Option>
                <Option value="4">
                    Recupération de Gain
                </Option>
                <Option value="5">
                    Expiration de Ticket
                </Option>
                <Option value="6">
                    Annulation de Ticket
                </Option>
            </>
        )
    }

    const getFields = () => {
        const children = [];
        children.push(
            <Row className={`${styles.fullWidthElement} w-100 d-flex`} gutter={24}>

                <Col span={12} key={"ticketsStatus"}>
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
                </Col>
                <Col span={12} key={"statusstatusstatus"}>
                <Form.Item
                        className={`${styles.formItem} searchTicketFormItem mb-5`}
                        name={`status`}
                        label={`Action sur le Ticket`}
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
                </Col>

                <Col span={12} key={`barCode`}>
                    <Form.Item
                        name={`date_range`}
                        label={`Période`}
                        initialValue=""
                        className={`${styles.formItem} searchTicketFormItem mb-5`}
                    >
                        <ConfigProvider locale={locale}>

                            <RangePicker
                                className={`${styles.datePickerDashboardHomePage} mt-2`}

                                onChange={handleDateChange as any}
                                placeholder={['Date de début', 'Date de fin']}
                                format={dateFormat}
                                cellRender={(current:any) => {
                                    const style: React.CSSProperties = {};
                                    if (current.date() === 1) {
                                        style.border = '1px solid #1677ff';
                                        style.borderRadius = '50%';
                                    }
                                    return (
                                        <div className="ant-picker-cell-inner" style={style}>
                                            {current.date()}
                                        </div>
                                    );
                                }}
                            />
                        </ConfigProvider>
                    </Form.Item>


                    {(userRole === 'ROLE_ADMIN' || userRole === 'ROLE_STOREMANAGER') && (
                        <>
                            <Form.Item
                                name={`client`}
                                label={
                                    inputLoading ? (
                                        <span>
                                          Client : <LoadingOutlined className={`mx-2`} />
                                        </span>
                                    ) : (
                                        'Client :'
                                    )
                                }
                                initialValue=""
                                colon={false}
                                className={`${styles.formItem} searchTicketFormItem mb-5`}
                            >
                                <Select showSearch
                                        onChange={onChangeClientList}
                                        filterOption={filterOption as any}
                                        className={`mt-2`} placeholder="Choisir un client"
                                        notFoundContent={
                                            <span className={`m-4`}>Aucun gain trouvé</span>
                                        }
                                        loading={inputLoading}
                                >
                                    {renderStoresClients()}
                                </Select>
                            </Form.Item>
                        </>
                    )}

                </Col>
                <Col span={12} key={`statusTicket`}>

                    {(userRole === 'ROLE_ADMIN') && (
                        <>
                            <Form.Item
                                name={`store`}
                                label={`Magasin`}
                                initialValue=""
                                className={`${styles.formItem} searchTicketFormItem mb-5`}
                            >
                                <Select
                                    className={`mt-2`}
                                    placeholder={`Magasin`}
                                    onChange={
                                        (e) => {
                                            setSearchParam({
                                                ...searchParam,
                                                store: e.toString(),
                                            });
                                        }
                                    }
                                    allowClear
                                >
                                    {renderStores()}
                                </Select>
                            </Form.Item>
                        </>
                    )}

                    {(userRole === 'ROLE_ADMIN' || userRole === 'ROLE_STOREMANAGER') && (
                        <>
                            <Form.Item
                                name={`employee`}
                                label={
                                    inputLoading ? (
                                        <span>
                                          Caissier : <LoadingOutlined className={`mx-2`} />
                                        </span>
                                    ) : (
                                        'Caissier :'
                                    )
                                }
                                initialValue=""
                                colon={false}
                                className={`${styles.formItem} searchTicketFormItem mb-5`}
                            >
                                <Select showSearch
                                        onChange={onChangeEmployeeList}
                                        filterOption={filterOption as any}
                                        className={`mt-2`} placeholder="Choisir un client"
                                        notFoundContent={
                                            <span className={`m-4`}>Aucun utilisateur trouvé</span>
                                        }
                                        loading={inputLoading}
                                >
                                    {renderStoresEmployees()}
                                </Select>
                            </Form.Item>
                        </>
                    )}



                </Col>




            </Row>,
        );

        return children;
    };


    const [showSpinner, setShowSpinner] = useState<boolean>(false);

    useEffect(() => {
        setShowSpinner(true);
            setTimeout(() => {
                setShowSpinner(false);
            }, 2000);

    }, [inputLoading]);
    const renderSearchForm = () => {
        return (
            <>
                <Form form={form} name="advanced_search" className={`${styles.searchTicketForm}`}>

                    {
                        showSpinner && (
                            <>
                                <Spin className={`${styles.formSpinner}`} size="small" />
                            </>
                        )
                    }

                    <Row className={`${styles.fullWidthElement}`} gutter={24}>{getFields()}</Row>
                    <div className={`mt-0 w-100`} style={{textAlign: 'right'}}>
                        <Space size="small">
                            <Button
                                className={`${styles.submitButtonBlue} mt-4`}

                                onClick={() => {
                                    form.resetFields();
                                    setSearchParam(defaultSearchParams);
                                }}
                            >
                                Réinitialiser
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
                    {userRole === 'ROLE_ADMIN' && (
                        <>
                            Historique des Tickets
                        </>
                    )}
                    {userRole === 'ROLE_STOREMANAGER' && (
                        <>
                            Historique des Tickets associés au magasin
                        </>
                    )}

                    {userRole === 'ROLE_CLIENT' && (
                        <>
                            Historique des Tickets associés au client
                        </>
                    )}
                    {userRole === 'ROLE_EMPLOYEE' && (
                        <>
                            Historique des Tickets associés au caissier
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
                                    <Col key={"resultTikcets"} className={`w-100 d-flex justify-content-between mt-3 px-4 ${styles.clientsTableColDashboard}`} xs={24} sm={24} md={24} lg={24} span={6}>
                                        <div className={`w-100 d-flex justify-content-between mt-3 px-4`}>
                                            <h6>
                                                Historique des Tickets
                                            </h6>
                                            <h6>
                                                {ticketHistoryList?.length}
                                                <span className={`mx-2`}>
                                                    sur
                                                 </span>
                                                {ticketHistoryListCount}

                                            </h6>
                                        </div>


                                        <div className={`${styles.clientsTableDashboard}`}>
                                            <HistoryTable key={ticketHistoryListCount} selectedStoreId={null} data={ticketHistoryList as any} ></HistoryTable>
                                        </div>


                                    </Col>

                                </>
                            )}

                        </Row>
                        {!loading && ticketHistoryListCount>0 &&
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
                                    defaultCurrent={parseInt(searchParam.page)} total={ticketHistoryListCount}/>
                            </Row>
                        }
                    </div>




                </div>
            </div>


        </div>
    );
}

export default TicketHistory;
