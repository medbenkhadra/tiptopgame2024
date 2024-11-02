import React, {useEffect, useState} from 'react';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import LogoutService from "@/app/service/LogoutService";
import {ColumnsType, TablePaginationConfig} from "antd/es/table";
import {Button, Checkbox, Col, ConfigProvider, Form, Modal, Row, Table, Tag} from "antd";
import {EditOutlined, StopOutlined} from "@ant-design/icons";
import {FilterValue, SorterResult} from "antd/es/table/interface";
import frFR from "antd/lib/locale/fr_FR";
import {getEmailServices, getEmailTemplatesVariables, getEmailTemplatesVariablesByService} from "@/app/api";

interface SeachParams {
    title?: string | null;
    type?: string | null;
}


const search: SeachParams = {
    title: '',
    type: '',
};

interface DataType {
    id: string;
    title: string;
    description: string;
    type: string;
    service: string;
    name: string;
    variables: any[];
}

interface DataTypeService {
    id: string;
    name: string;
    label: string;
    description: string;
    templates: [];
}

interface DataTypeTemplateVariable {
    id: string;
    name: string;
    services: [];
}


interface SeachParamsServices {
    label?: string | null;
    description?: string | null;
}

interface TableParamsTemplateVariable {
    pagination?: TablePaginationConfig;
    sortField?: string;
    sortOrder?: string;
    filters?: Record<string, FilterValue>;
}


const searchServices: SeachParamsServices = {
    label: null,
    description: null,
};


interface SettingsCorrespandancesTemplatesProps {
    selectTab: (key: string) => void;
}

function ServicesCorrespandancesTemplates({selectTab}: SettingsCorrespandancesTemplatesProps) {
    const [formRef] = Form.useForm();

    const {logoutAndRedirectAdminsUserToLoginPage} = LogoutService();

    const [loading, setLoading] = useState(false);


    const [servicesData, setServicesData] = useState<DataTypeService[]>();


    function getEmailServicesData() {
        getEmailServices().then((res) => {
            setServicesData(res);
            console.log("res.data", res);
        }).catch((err) => {
            console.log("err", err);
            if (err.response) {
                if (err.response.status === 401) {
                    logoutAndRedirectAdminsUserToLoginPage();
                }
            }
        });


    }


    useEffect(() => {
        getEmailServicesData();
    }, []);


    const [templateVariableDataByService, setTemplateVariableDataByService] = useState<DataTypeTemplateVariable[]>();
    const [tableTemplateVariableParamsByService, setTableTemplateVariableParamsByService] = useState<TableParamsTemplateVariable>({
        pagination: {
            current: 1,
            pageSize: 15,
        },
    });

    const handleTemplateVariableByServiceTableChange = (
        pagination: TablePaginationConfig,
        filters: Record<string, FilterValue>,
        sorter: SorterResult<DataType>,
    ) => {
        setTableTemplateVariableParamsByService({
            pagination,
            filters,
            ...sorter,
        });

        if (pagination.pageSize !== tableTemplateVariableParamsByService.pagination?.pageSize) {
            setTemplateVariableDataByService([]);
        }
    };

    function getEmailTemplatesVariablesData() {
        getEmailTemplatesVariablesByService(tableTemplateVariableParamsByService).then((res) => {
            setTemplateVariableDataByService(res);
            console.log("res.data", res);
        }).catch((err) => {
            console.log("err", err);
            if (err.response) {
                if (err.response.status === 401) {
                    logoutAndRedirectAdminsUserToLoginPage();
                }
            }
        });


    }


    useEffect(() => {
        getEmailTemplatesVariablesData();
    }, [tableTemplateVariableParamsByService]);


    const [variablesData, setVariablesData] = useState<DataTypeTemplateVariable[]>();
    useEffect(() => {
        getEmailTemplatesVariables(tableTemplateVariableParamsByService).then((res) => {
            setVariablesData(res);
            console.log("res.data", res);
        }).catch((err) => {
            console.log("err", err);
            if (err.response) {
                if (err.response.status === 401) {
                    logoutAndRedirectAdminsUserToLoginPage();
                }
            }
        });
    }, []);


    const columnsServices: ColumnsType<DataTypeService> = [
        {
            key: 'id-service',
            title: 'Nom de service',
            dataIndex: 'label',
            render: (label) => `${label}`,
            className: `${styles.lastnameColProfileManagement}`,
        },

        {
            key: 'description-service',
            title: 'Description',
            dataIndex: 'description',
            render: (firstname) => `${firstname}`,
            className: `${styles.firstnameColProfileManagement}`,
        },

        {
            key: 'nbr-templates-service',
            title: 'Nbr de templates',
            dataIndex: 'templates',
            render: (templates) => `${templates?.length}`,
            className: `${styles.firstnameColProfileManagement}`,
            width: '20%',
            align: 'center',

        },


    ];


    const [checkedVariables, setCheckedVariables] = useState<any[]>([]);

    function getServiceTagColor(name: string) {
        if (name.includes("client")) {
            return "blue";
        }
        if (name.includes("employé")) {
            return "green";
        }

        if (name.includes("store")) {
            return "orange";
        }
        if (name.includes("reset")) {
            return "red";
        }

        if (name.includes("ticket")) {
            return "cyan";
        }

        return "purple";

    }


    const columnsTemplateVariable: ColumnsType<DataTypeTemplateVariable> = [
        {
            key: 'id-template-variable',
            title: 'ID',
            dataIndex: 'variables',
            render: (variables) => <>
                <Button onClick={() => {
                    setVariablesModalVisible(true);
                    let array: any = [];
                    variables.forEach((variable: any) => {
                        array.push(variable.name);
                    });
                    setCheckedVariables(array);
                }} className={`${styles.updateStoreArrayBtn}`} icon={<EditOutlined/>} size={"middle"}>
                    Modifier
                </Button>
            </>,

            className: `${styles.lastnameColProfileManagement}`,
            width: '15%',
        },

        {
            key: 'name-template-variable',
            title: 'Désignation',
            dataIndex: 'label',
            render: (label) => <>
                <span className={`${styles.tableTdText}`}>{label}</span>
            </>,
            className: `${styles.firstnameColProfileManagement}`,
        },


        {
            width: '50%',
            key: 'services-template-variable',
            title: 'Variables incluses',
            dataIndex: 'variables',
            render: (variables) => <>
                {variables?.map((variable: any) => (
                    <Tag
                        className={`${styles.serviceTag}`}
                        color={getServiceTagColor(variable.name)}
                    >{variable.name}</Tag>
                ))}
                {!variables && <Tag color="red">Aucune variable</Tag>}
            </>,
        },


    ];


    const customEmptyTextServiceTable = (
        <div className={styles.emptyTableTextDiv}>
            <span>
                Aucun service n'est disponible pour le moment.
            </span>
            <span><StopOutlined/></span>
        </div>
    );


    const customEmptyTextEmailVariableTable = (
        <div className={styles.emptyTableTextDiv}>
            <span>
                Aucune variable n'est disponible pour le moment.
            </span>
            <span><StopOutlined/></span>
        </div>
    );


    const [variablesModalVisible, setVariablesModalVisible] = useState(false);

    const handleCheckboxChange = (variableNam: any) => {
        const currentIndex = checkedVariables.indexOf(variableNam);
        const newChecked = [...checkedVariables];

        if (currentIndex === -1) {
            newChecked.push(variableNam);
        } else {
            newChecked.splice(currentIndex, 1);
        }

        setCheckedVariables(newChecked);
    };


    return (
        <div className={`${styles.fullWidthElement}`}>
            <Row className={`${styles.fullWidthElement} mt-3`}>
                <Col
                    className={`${styles.fullWidthElement} d-flex justify-content-between align-items-center mt-3 mb-4 `}>
                    <h6 className={`${styles.storeAdminDashboardTitle}`}>
                        Liste des variables disponibles
                    </h6>
                </Col>

                <Col className={`${styles.fullWidthElement} servicesTable`}>
                    <ConfigProvider locale={frFR}>
                        <Table
                            locale={{emptyText: customEmptyTextEmailVariableTable}}
                            columns={columnsTemplateVariable as any}
                            rowKey={(record) => record.id}
                            dataSource={templateVariableDataByService}
                            pagination={tableTemplateVariableParamsByService.pagination}
                            loading={loading}
                            onChange={handleTemplateVariableByServiceTableChange as any}
                        />
                    </ConfigProvider>
                </Col>
            </Row>


            <Row className={`${styles.fullWidthElement} mt-3`}>
                <Col
                    className={`${styles.fullWidthElement} d-flex justify-content-between align-items-center mt-3 mb-4 `}>
                    <h6 className={`${styles.storeAdminDashboardTitle}`}>
                        Liste des services
                    </h6>
                </Col>

                <Col className={`${styles.fullWidthElement} servicesTable`}>
                    <ConfigProvider locale={frFR}>
                        <Table
                            locale={{emptyText: customEmptyTextServiceTable}}
                            columns={columnsServices as any}
                            rowKey={(record) => record.id}
                            dataSource={servicesData}
                            pagination={false}
                            loading={loading}
                        />
                    </ConfigProvider>
                </Col>
            </Row>


            <Modal
                title="Variables"
                centered
                visible={variablesModalVisible}
                onOk={() => setVariablesModalVisible(false)}
                onCancel={() => setVariablesModalVisible(false)}
                width={1000}
                footer={[
                    <Button key="back" onClick={() => setVariablesModalVisible(false)}>
                        Annuler
                    </Button>,
                    <Button key="submit" type="primary" onClick={() => setVariablesModalVisible(false)}>
                        Enregistrer
                    </Button>,
                ]}
            >
                <Row className={`${styles.fullWidthElement} mt-3`}>
                    <Col
                        className={`${styles.fullWidthElement} d-flex justify-content-between align-items-center mt-3 mb-4 `}>
                        <h6 className={`${styles.storeAdminDashboardTitle}`}>
                            Liste des variables disponibles
                        </h6>
                    </Col>

                    <Col className={`${styles.fullWidthElement} servicesTable`}>
                        {variablesData?.map((variable) => (
                            <>
                                <Checkbox
                                    key={variable.name}
                                    checked={checkedVariables.includes(variable.name)}
                                    onChange={() => handleCheckboxChange(variable.name)}
                                    className={`${styles.checkboxVariable} my-2 mx-2`}
                                >
                                    <Tag color={getServiceTagColor(variable.name)}>{variable.name}</Tag>
                                </Checkbox>
                            </>
                        ))}

                        {!variablesData && <Tag color="red">Aucune variable</Tag>}

                    </Col>
                </Row>

            </Modal>


        </div>
    );
}

export default ServicesCorrespandancesTemplates;