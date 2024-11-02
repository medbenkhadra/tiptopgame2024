import React, {useEffect, useState} from 'react';

import {
    Button,
    Col,
    ConfigProvider,
    DatePicker,
    DatePickerProps,
    Form,
    Pagination,
    Row,
    Select,
    Space,
    Spin,
    theme
} from "antd";
import styles from '@/styles/pages/dashboards/storeAdminDashboard.module.css';
import {getGainTicket, getParticipants, getPrizes, getStoresEmployees, getStoresForAdmin} from "@/app/api";
import LogoutService from "@/app/service/LogoutService";
import locale from "antd/locale/fr_FR";
import GainsTable from "@/app/components/dashboardComponents/GameGainHistory/components/GainsTable";
import {DataType} from "csstype";

const {Option} = Select;

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
        dob: string;
    };

    user: {
        id: string;
        firstname: string;
        lastname: string;
        email: string;
        dob: string;
    };

    store: {
        id: string;
        name: string;
        address: string;
        phone: string;
        email: string;
    };

    prize: {
        id: string;
        name: string;
        label: string;
        prize_value: string;
        winning_rate: string;
    };

}
interface SearchParams {
    page: string;
    limit: string;
    store: string;
    user: string;
    status: string;
    employee: string;
    client: string;
    sort: string;
    order: string;
    prize: string;
    start_date?: string;
    end_date?: string;
}

const defaultSearchParams: SearchParams = {
    page: '1',
    limit: '12',
    store: '',
    user: '',
    status: '4',
    employee: '',
    client: '',
    sort: '',
    order: '',
    prize: '',
    start_date: '',
    end_date: '',
};

const dateFormat = 'DD/MM/YYYY';


function GameGainHistoryPage() {
    const { RangePicker } = DatePicker;



    const [userRole, setUserRole] = useState<string>('');
    const [loading, setLoading] = useState<boolean>(false);

    useEffect(() => {
        const userRoleSaved = localStorage.getItem("loggedInUserRole");
        if (userRoleSaved) {
            setUserRole(userRoleSaved);
        }
    }, []);
    const [searchParam, setSearchParam] = useState<SearchParams>(defaultSearchParams);

    const [selectedStoreId, setSelectedStoreId] = useState<string>('');



    const [form] = Form.useForm();





    const {logoutAndRedirectAdminsUserToLoginPage} = LogoutService();
    const [prizesList, setPrizesList] = useState<any[]>([]);
    function getAllPrizes() {
        setLoading(true);
        getPrizes().then((response) => {
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
        getAllStoresClients();
        getAllStoresEmployees();
        getAllStores();
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

    const [storesClientsList, setStoresClientsList] = useState<any[]>([]);
    const [storesEmployeesList, setStoresEmployeesList] = useState<any[]>([]);
    const [storesList, setStoresList] = useState<any[]>([]);
    const [gainTicketsList, setGainTicketsList] = useState<DataType[]>([]);
    const [gainTicketsCount, setGainTicketsCount] = useState<number>(0);

    useEffect(() => {
        getGainTickets();
    }, [searchParam]);
    function getGainTickets() {
        setLoading(true);
        getGainTicket(searchParam).then((response) => {
            setGainTicketsList(response.gains);
            setGainTicketsCount(response.totalCount);
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

    function getAllStoresEmployees() {
        setLoading(true);
        getStoresEmployees(searchParam).then((response) => {
            setStoresEmployeesList(response.users);
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
    function getAllStoresClients() {
        setLoading(true);
        getParticipants(searchParam).then((response) => {
            setStoresClientsList(response.users);
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

    function getAllStores() {
        setLoading(true);
        getStoresForAdmin().then((response) => {
            setStoresList(response.storesResponse);
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

    const renderStoresClients = () => {
        return (
            <>
                <Option value="">
                    Tous les Clients
                </Option>
                {storesClientsList.map((client, key) => {
                    return (
                        <Option key={key+'_client'} value={client.id}   label={`${client.lastname} ${client.firstname}`} >{client.lastname} {client.firstname}</Option>
                    )
                })}
            </>
        )
    }

    function renderStoresEmployees() {
        return (
            <>
                <Option value="">
                    Tous les Caissiers
                </Option>
                {storesEmployeesList.map((employee, key) => {
                    return (
                        <Option key={key+'_employee'} value={employee.id} label={`${employee.lastname} ${employee.firstname}`} >{employee.lastname} {employee.firstname}</Option>
                    )
                })}
            </>
        )
    }

    const handleDateChange: DatePickerProps['onChange'] = (date, dateString) => {
        console.log(dateString, date)
        if (dateString && date) {
            console.log(date.format('DD/MM/YYYY'));
            let ch = date.format('DD/MM/YYYY');
            console.log(ch);
        }
    };
    const onChangeEmployeeList = (value: string) => {
        setSearchParam({
            ...searchParam,
            employee: value,
        });
    };

    const onChangeClientList = (value: string) => {
        setSearchParam({
            ...searchParam,
            client: value,
        });
    };
    const renderStores = () => {
        return (
            <>
                <Option value="">
                    Tous les Magasins
                </Option>
                {storesList.map((store, key) => {
                    return (
                        <Option key={key+'_store'} value={store.id}>{store.name}</Option>
                    )
                })}
            </>
        )
    }

    function onSearchEmpoyeeList() {

    }

    function onSearchClientList() {

    }

    const filterOption = (input: string, item: any) => (item?.label ?? '').toLowerCase().includes(input.toLowerCase());

    const getFields = () => {
        const children = [];
        children.push(
            <Row className={`${styles.fullWidthElement} w-100 d-flex`} gutter={24}>
                <Col span={12} key={`barCodeInput`}>

                <Form.Item
                        name={`date_range`}
                        label={`Période`}
                        initialValue=""
                        className={`${styles.formItem} searchTicketFormItem mb-5`}
                    >
                    <ConfigProvider locale={locale}>

                        <RangePicker
                            className={`${styles.datePickerDashboardHomePage} mt-2`}

                            onChange={(date , dateString ) => {
                                if (dateString && date) {
                                    setSearchParam({
                                        ...searchParam,
                                        start_date: dateString[0],
                                        end_date: dateString[1],
                                    });
                                }
                            }}
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
                                        {
                                        current.date()}
                                    </div>
                                );
                            }}
                        />
                    </ConfigProvider>
                    </Form.Item>


                    {(userRole === 'ROLE_ADMIN' || userRole === 'ROLE_STOREMANAGER') && (
                        <>
                            <Form.Item
                                name={`user`}
                                label={`Client`}
                                initialValue=""
                                className={`${styles.formItem} searchTicketFormItem mb-5`}
                            >
                                <Select showSearch
                                        onChange={onChangeClientList}
                                        onSearch={onSearchClientList}
                                        filterOption={filterOption as any}
                                        className={`mt-2`} placeholder="Choisir un client"
                                        notFoundContent={
                                            <span className={`m-4`}>Aucun gain trouvé</span>
                                        }
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
                            name={`caissier`}
                            label={`Caissier`}
                            initialValue=""
                            className={`${styles.formItem} searchTicketFormItem mb-5`}
                        >
                            <Select showSearch
                                    onChange={onChangeEmployeeList}
                                    onSearch={onSearchEmpoyeeList}
                                    filterOption={filterOption as any}
                                    className={`mt-2`} placeholder="Choisir un client"
                                    notFoundContent={
                                        <span className={`m-4`}>Aucun gain trouvé</span>
                                    }
                            >
                                {renderStoresEmployees()}
                            </Select>
                        </Form.Item>
                        </>
                    )}

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
                            Historique des gains
                        </>
                    )}
                    {userRole === 'ROLE_STOREMANAGER' && (
                        <>
                            Historique des gains par magasin
                        </>
                    )}

                    {userRole === 'ROLE_CLIENT' && (
                        <>
                            Historique des gains associés au client
                        </>
                    )}
                    {userRole === 'ROLE_EMPLOYEE' && (
                        <>
                            Historique des gains associés au caissier
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
                                                Résultat de recherche
                                            </h6>
                                            <h6>
                                                {gainTicketsList?.length} ticket(s) gagnant(s) sur {gainTicketsCount}

                                            </h6>
                                        </div>


                                        <div className={`${styles.clientsTableDashboard}`}>
                                            <GainsTable key={gainTicketsCount} selectedStoreId={null} data={gainTicketsList as any} ></GainsTable>
                                        </div>


                                    </Col>

                                </>
                            )}

                        </Row>
                        {!loading && gainTicketsCount>0 &&
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
                                    defaultCurrent={parseInt(searchParam.page)} total={gainTicketsCount}/>
                            </Row>
                        }
                    </div>




                </div>
            </div>


        </div>
    );
}

export default GameGainHistoryPage;
