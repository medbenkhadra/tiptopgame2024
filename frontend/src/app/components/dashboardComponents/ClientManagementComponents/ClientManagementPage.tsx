import React, {useEffect, useState} from 'react';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import {Button, Col, Form, Input, Pagination, Row, Select, Space, Spin, theme} from 'antd';
import {getClients, getPrizes} from "@/app/api";
import LogoutService from "@/app/service/LogoutService";
import {DownOutlined} from "@ant-design/icons";
import StoresList from "@/app/components/dashboardComponents/TicketsPageComponent/components/StoresList";
import ClientTable from "@/app/components/dashboardComponents/ClientManagementComponents/components/ClientsTable";

const {Option} = Select;

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
    client: string;
    caissier: string;
    store: string;


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
    lastname: string;
    firstname: string;
    sort: string;
    order: string;
    email?: string;
    genre?: string;
}

const defaultSearchParams: SearchParams = {
    page: '1',
    limit: '12',
    store: '',
    user: '',
    status: '',
    lastname: '',
    firstname: '',
    sort: '',
    order: '',
    email: '',
    genre: '',
};

function ClientManagementPage() {

    const {logoutAndRedirectAdminsUserToLoginPage} = LogoutService();

    const [data, setData] = useState<DataType[]>();
    const [loading, setLoading] = useState(false);
    const [searchParam, setSearchParam] = useState<SearchParams>(defaultSearchParams);
    const [clientsCount, setClientsCount] = useState(0);

    const [resultCount, setResultCount] = useState(0);

    function fetchData() {
        setLoading(true);
        getClients(searchParam).then((response) => {
            console.log('response : ', response);
            setData(response.users);
            setClientsCount(response.totalCount);
            setResultCount(response.resultCount);
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
    const [userRole , setUserRole] = useState<string | null>('');
    useEffect(() => {
        setUserRole(localStorage.getItem('loggedInUserRole'));
    }, []);



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
                    Tous les comptes
                </Option>
                <Option  value="1">
                    Ouvert
                </Option>
                <Option value="2">
                    Fermé
                </Option>
            </>
        )
    }

    const [prizesList, setPrizesList] = useState<PrizeType[]>([]);
    function getAllPrizes() {
        setLoading(true);
        getPrizes().then((response) => {
            console.log('response : ', response);
            setPrizesList(response.prizes);
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
                        <Option key={key} value={prize.id}>{prize.label}</Option>
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
                <Col span={8} key={`lastnameclient`}>
                    <Form.Item
                        className={`${styles.formItem} searchTicketFormItem mb-5`}
                        name={`code`}
                        label={`Nom de Client`}
                    >
                        <Input className={`mt-2`} placeholder="Nom,.."
                               onChange={(e) => {
                                   setSearchParam({
                                       ...searchParam,
                                       lastname: e.target.value,
                                       page: '1',
                                   });
                               }}
                        />
                    </Form.Item>

                    <Form.Item
                        className={`${styles.formItem} searchTicketFormItem mb-5`}
                        name={`client`}
                        label={`Prénom de Client`}
                        initialValue=""
                    >
                        <Input className={`mt-2`}
                               placeholder="Prénom,.."
                               onChange={(e) => {
                                   setSearchParam({
                                       ...searchParam,
                                       firstname: e.target.value,
                                       page: '1',
                                   });
                               }}
                        />
                    </Form.Item>

                </Col>
                <Col span={8} key={`status`}>


                    <Form.Item
                        className={`${styles.formItem} searchTicketFormItem mb-5`}
                        name={`status`}
                        label={`Statut de Compte`}
                        initialValue=""
                    >

                        <Select placeholder={`Tous les Statuts`} value={searchParam.status} onChange={(e) => {
                            setSearchParam({
                                ...searchParam,
                                status: e.toString(),
                                page: '1',
                            });
                        }} className={`mt-2`}>
                            {renderTicketsStatus()}
                        </Select>
                    </Form.Item>

                    <Form.Item
                        className={`${styles.formItem} searchTicketFormItem mb-5`}
                        name={`email`}
                        label={`Adresse E-mail`}
                    >
                        <Input className={`mt-2`} placeholder="E-mail,.."
                               onChange={(e) => {
                                   setSearchParam({
                                       ...searchParam,
                                       email: e.target.value,
                                       page: '1',
                                   });
                               }}
                        />
                    </Form.Item>


                </Col>

                <Col span={8} key={`stores`}>
                    {userRole === 'ROLE_ADMIN' && (
                        <>
                            {renderStores()}
                        </>
                    )}



                    {expand && (
                        <>
                            <Form.Item
                                name={`sexe`}
                                label={`Genre`}
                                initialValue=""
                                className={`${styles.formItem} searchTicketFormItem mb-5`}
                            >
                                <Select onChange={
                                    (e) => {
                                        setSearchParam({
                                            ...searchParam,
                                            genre: e.toString(),
                                            page: '1',
                                        });
                                    }
                                } placeholder={`Tous les Genres`
                                } className={`mt-2`}>
                                    <Option value="">
                                        Tous
                                    </Option>
                                    <Option value="Homme">
                                        Homme
                                    </Option>
                                    <Option value="Femme">
                                        Femme
                                    </Option>
                                    <Option value="Autre">
                                        Autre
                                    </Option>
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
                        </Space>
                    </div>
                </Form>
            </>
        );

    }

    return (
        <div className={styles.homePageContent}>

            <div className={`${styles.homePageContentTopHeader}`}>
                <h1 className={`mx-3`}>Clients Inscrits
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
                                             {data?.length} Utilisateur(s) trouvé(s) sur {clientsCount}

                                       </h6>
                                   </div>


                                           <div className={`${styles.clientsTableDashboard}`}>
                                               <ClientTable key={clientsCount} selectedStoreId={"3"} data={data as any} ></ClientTable>
                                           </div>


                                   </Col>

                               </>
                               )}

                        </Row>
                        {!loading && clientsCount>0 &&
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
                                    defaultCurrent={parseInt(searchParam.page)} total={clientsCount}/>
                            </Row>
                        }


                    </div>

                </div>
            </div>


        </div>
    );
}

export default ClientManagementPage;