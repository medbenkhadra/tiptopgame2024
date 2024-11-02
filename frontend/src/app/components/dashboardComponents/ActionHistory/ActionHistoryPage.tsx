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
    Spin
} from "antd";
import styles from '@/styles/pages/dashboards/storeAdminDashboard.module.css';
import {
    getActionsHistory,
    getFiltredUsers,
    getGainTicket,
    getParticipants,
    getStoresEmployees,
    getStoresForAdmin
} from "@/app/api";
import LogoutService from "@/app/service/LogoutService";
import locale from "antd/locale/fr_FR";
import {DataType} from "csstype";
import HistoryTable from "@/app/components/dashboardComponents/ActionHistory/components/HistoryTable";

const {Option} = Select;

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
interface SearchParams {
    page: string;
    limit: string;
    store: string;
    user: string;
    action_type: string;
    sort: string;
    order: string;
    start_date?: string;
    end_date?: string;
    role?: string;
}

const defaultSearchParams: SearchParams = {
    page: '1',
    limit: '12',
    store: '',
    user: '',
    action_type: '',
    sort: '',
    order: '',
    start_date: '',
    end_date: '',
    role: '',
};

const dateFormat = 'DD/MM/YYYY';


function ActionHistoryPage() {
    const {logoutAndRedirectAdminsUserToLoginPage} = LogoutService();
    const [form] = Form.useForm();

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
    const handleStoreChange = (value: string) => {
        setSelectedStoreId(value);
        setSearchParam({
            ...searchParam,
            store: value,
        });
    };


    const [actionsHistoryList, setActionsHistoryList] = useState<DataType[]>([]);
    const [actionsHistoryListCount , setActionsHistoryListCount] = useState<number>(0);


    useEffect(() => {
        getAllStores();
    },[]);

    useEffect(() => {
        getHistoryActions();
    }, [searchParam]);

    function getHistoryActions() {
        setLoading(true);
        getActionsHistory(searchParam).then((response) => {
            console.log('response : ', response);
            setActionsHistoryList(response.actionsHistory);
            setActionsHistoryListCount(response.actionsHistoryCount);
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




    const [usersList, setUsersList] = useState<any[]>([]);
    const [storesList, setStoresList] = useState<any[]>([]);


    useEffect(() => {
        form.resetFields(['user']);
        getUsers();
    },[searchParam.role , searchParam.store]);
    function getUsers() {
        setLoading(true);
        getFiltredUsers(searchParam).then((response) => {
            console.log('response : ', response);
            setUsersList(response.users);
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

    const renderUsersList = () => {
        return (
            <>
                <Option value="">
                    Tous les Utilisateurs
                </Option>
                {usersList.map((client, key) => {
                    return (
                        <Option key={key} value={client.id}   label={`${client.lastname} ${client.firstname}`} >{client.lastname} {client.firstname}</Option>
                    )
                })}
            </>
        )
    }


    useEffect(() => {
        if (searchParam.store!=="" && userRole === 'ROLE_ADMIN' && (searchParam.role==="ROLE_BAILIFF" || searchParam.role==="ROLE_ADMIN")) {
            setSearchParam({
                ...searchParam,
                role: "",
            });
            form.resetFields(['role']);
        }



    }, [searchParam.store]);

    const renderRolesList = () => {
        return (
            <>
                <Option value="">
                    Tous les Rôles
                </Option>
                {(userRole === 'ROLE_ADMIN' && searchParam.store=="")&& (
                    <>
                        <Option value="ROLE_ADMIN">
                            ADMIN
                        </Option>
                    </>
                )}
                {userRole === 'ROLE_ADMIN' && (
                    <>
                        <Option value="ROLE_STOREMANAGER">
                            STORE MANAGER
                        </Option>
                    </>
                )}

                {(userRole === 'ROLE_ADMIN' || userRole === 'ROLE_STOREMANAGER') && (
                    <>
                        <Option value="ROLE_EMPLOYEE">
                            EMPLOYEE
                        </Option>
                    </>
                )}

                <Option value="ROLE_CLIENT">
                    CLIENT
                </Option>


                {(userRole === 'ROLE_ADMIN' && searchParam.store=="") && (
                    <>
                        <Option value="ROLE_BAILIFF">
                            L'HUISSIER
                        </Option>
                    </>
                )}

            </>
        )
    }



    const handleDateChange: DatePickerProps['onChange'] = (date, dateString) => {
        console.log(date, dateString);
        if (dateString && date) {
            setSearchParam((prevState) => ({
                ...prevState,
                start_date: dateString[0],
                end_date: dateString[1],
            }));
        }
    };

    const onChangeUser = (value: string) => {
        setSearchParam({
            ...searchParam,
            user: value,
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
                        <Option key={key} value={store.id}>{store.name}</Option>
                    )
                })}
            </>
        )
    }


    const filterOption = (input: string, item: any) => (item?.label ?? '').toLowerCase().includes(input.toLowerCase());

    const getFields = () => {
        const children = [];
        children.push(
            <Row className={`${styles.fullWidthElement} w-100 d-flex`} gutter={24}>
                <Col span={12} key={`actions`}>

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
                                name={`user`}
                                label={`Utilisateur lié à l'action`}
                                initialValue=""
                                className={`${styles.formItem} searchTicketFormItem mb-5`}
                            >
                                <Select showSearch
                                        onChange={onChangeUser}
                                        filterOption={filterOption as any}
                                        className={`mt-2`} placeholder="Choisir un utilisateur"
                                        notFoundContent={
                                            <span className={`m-4`}>Aucun utilisateur trouvé</span>
                                        }
                                >
                                    {renderUsersList()}
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
                                key={`${searchParam.role}`}
                                name={`role`}
                                label={`Rôle de l'utilisateur lié à l'action`}
                                initialValue=""
                                className={`${styles.formItem} searchTicketFormItem mb-5`}
                            >
                                <Select showSearch
                                        onChange={
                                            (e) => {
                                               if (e) {
                                                   setSearchParam({
                                                       ...searchParam,
                                                       role: e.toString(),
                                                   });
                                               }else {
                                                    setSearchParam({
                                                         ...searchParam,
                                                         role: "",
                                                    });
                                                  }
                                            }
                                        }
                                        filterOption={filterOption as any}
                                        className={`mt-2`} placeholder="Choisir un rôle"
                                        notFoundContent={
                                            <span className={`m-4`}>Aucun role trouvé</span>
                                        }
                                >
                                    {renderRolesList()}
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
                            Historique des actions
                        </>
                    )}
                    {userRole === 'ROLE_STOREMANAGER' && (
                        <>
                            Historique des actions associés au magasin
                        </>
                    )}

                    {userRole === 'ROLE_CLIENT' && (
                        <>
                            Historique des actions associés au client
                        </>
                    )}
                    {userRole === 'ROLE_EMPLOYEE' && (
                        <>
                            Historique des actions associés au caissier
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
                                                Historique des actions
                                            </h6>
                                            <h6>
                                                {actionsHistoryList?.length}
                                                 <span className={`mx-2`}>
                                                     Actions trouvées sur un total de
                                                 </span>
                                                {actionsHistoryListCount}

                                            </h6>
                                        </div>


                                        <div className={`${styles.clientsTableDashboard}`}>
                                            <HistoryTable key={actionsHistoryListCount} selectedStoreId={null} data={actionsHistoryList as any} ></HistoryTable>
                                        </div>


                                    </Col>

                                </>
                            )}

                        </Row>
                        {!loading && actionsHistoryListCount>0 &&
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
                                    defaultCurrent={parseInt(searchParam.page)} total={actionsHistoryListCount}/>
                            </Row>
                        }
                    </div>




                </div>
            </div>


        </div>
    );
}

export default ActionHistoryPage;
