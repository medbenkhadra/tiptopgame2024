import React, {useEffect, useState} from 'react';
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import LogoutService from "@/app/service/LogoutService";
import {ColumnsType, TablePaginationConfig} from "antd/es/table";
import {Button, Col, ConfigProvider, Form, Input, Modal, Row, Select, Space, Table, Tag} from "antd";
import {DeleteOutlined, EditOutlined, PlusOutlined, StopOutlined} from "@ant-design/icons";
import {FilterValue, SorterResult} from "antd/es/table/interface";
import frFR from "antd/lib/locale/fr_FR";
import {createNewTemplate, getCorrespandanceTemplates} from "@/app/api/endpoints/EmailTemplatesApi";
import {getEmailServices} from "@/app/api";


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
    service: any;
    name: string;
    variables: any[];
    required: boolean;
}



interface DataTypeService {
    id: string;
    name: string;
    label: string;
    description: string;
}



interface TableParams {
    pagination?: TablePaginationConfig;
    sortField?: string;
    sortOrder?: string;
    filters?: Record<string, FilterValue>;
}


interface TableParamsServices {
    pagination?: TablePaginationConfig;
    sortField?: string;
    sortOrder?: string;
    filters?: Record<string, FilterValue>;
}





interface SettingsCorrespandancesTemplatesProps {
    selectTab: (key: string) => void;
    selectTemplate: (value: string) => void;
}

function SettingsCorrespandancesTemplates({selectTab,selectTemplate}: SettingsCorrespandancesTemplatesProps) {
    const [formRef] = Form.useForm();

    const {logoutAndRedirectAdminsUserToLoginPage} = LogoutService();

    const [loading, setLoading] = useState(false);

    const [templateArray, setTemplatesArray] = useState<any[]>([]);
    const [searchForm, setSearchForm] = useState<SeachParams>(search);

    const [formTemplate, setFormTemplate] = useState<DataType>({
        id: "",
        title: "",
        description: "",
        type: "",
        service: "",
        name: "",
        variables:[],
        required: false,
    });

    const getTemplates = () => {
        getCorrespandanceTemplates(searchForm).then((res) => {
            let templates:any = [];
                res.forEach((template:any) => {
                    templates.push(template);
                });

                console.log("templatestemplates", templates);
                setTemplatesArray(templates);

        }).catch((err) => {
            console.log("err", err);
            if (err.response) {
                if (err.response.status === 401) {
                    logoutAndRedirectAdminsUserToLoginPage();
                }
            }
        });
    }


    const [data, setData] = useState<DataType[]>();
    const [tableParams, setTableParams] = useState<TableParams>({
        pagination: {
            current: 1,
            pageSize: 15,
        },
    });

    const handleTableChange = (
        pagination: TablePaginationConfig,
        filters: Record<string, FilterValue>,
        sorter: SorterResult<DataType>,
    ) => {
        setTableParams({
            pagination,
            filters,
            ...sorter,
        });

        if (pagination.pageSize !== tableParams.pagination?.pageSize) {
            setData([]);
        }
    };




    useEffect(() => {
        getTemplates();

    }, []);











    const customEmptyText = (
        <div className={styles.emptyTableTextDiv}>
            <span>
                Aucun Template de correspandance n'est disponible pour le moment.
            </span>
            <span><StopOutlined/></span>
        </div>
    );



    const [isModalVisible, setIsModalVisible] = useState(false);

    function addNewTemplate() {
        setIsModalVisible(true);
    }

    const handleFormChange = (fieldName: any, value: any) => {
        setFormTemplate((prevData) => ({...prevData, [fieldName]: value}));
    };

    const [CreateEmailTemplateError, setCreateEmailTemplateError] = useState(false);

    const handleOk = () => {
        handleFormOk();
    }
    const handleFormOk = () => {
        setCreateEmailTemplateError(false);
        console.log("formTemplate", formTemplate);
        if (formTemplate.title === "") {
            setCreateEmailTemplateError(true);
            return;
        }
        if (formTemplate.description === "") {
            setCreateEmailTemplateError(true);
            return;
        }
        if (formTemplate.type === "") {
            setCreateEmailTemplateError(true);
            return;
        }
        if (formTemplate.service === "") {
            setCreateEmailTemplateError(true);
            return;
        }

        createNewTemplate(formTemplate).then((res) => {
                console.log("res.data", res);
                setIsModalVisible(false);
                getTemplates();
                setFormTemplate(
                    {
                        id: "",
                        title: "",
                        description: "",
                        type: "",
                        service: "",
                        name: "",
                        variables:[],
                        required: false,
                    }
                );

                formRef.resetFields();
                getTemplates();
        }).catch((err) => {
            console.log("err", err);
        })

    };

    const handleCancel = () => {
        setFormTemplate(
            {
                id: "",
                title: "",
                description: "",
                type: "",
                service: "",
                name: "",
                variables:[],
                required: false,
            }
        );

        formRef.resetFields();
        console.log("formTemplate", formTemplate);
        setIsModalVisible(false);
    };


    function getTagColor(id:any) {
        id = id.toString();
        if(id == "1"){
            return "blue";
        }
        if(id === "2"){
            return "green";
        }

        if(id === "3"){
            return "orange";
        }
        if(id === "4"){
            return "red";
        }

        if(id === "5"){
            return "cyan";
        }

        if(id === "6"){
            return "purple";
        }

        if(id === "7"){
            return "magenta";
        }

        if(id === "8"){
            return "geekblue";
        }

        if(id === "9"){
            return "gold";
        }

        if(id === "10"){
            return "lime";
        }

        if(id === "11"){
            return "volcano";
        }

        if(id === "12"){
            return "yellow";
        }

        if(id === "13"){
            return "default";
        }

        return "purple";
    }

    const columns: ColumnsType<DataType> = [
        {
            key: 'id-template',
            title: 'ID',
            dataIndex: 'id',
            sorter: true,
            render: (id) => <><span>{id}</span></>,
            className: `${styles.tableTd}`,
            width: '5%',
        },
        {
            key: 'name-template',
            title: 'Nom de template',
            dataIndex: 'name',
            width: '30%',
            sorter: true,
            render: (name) => <><span className={`${styles.tableTdText}`}>{name}</span></>,
            className: `${styles.tableTd}`,
        },
        {
            key: 'variables-template',
            title: 'Service',
            dataIndex: 'service',
            render: (service) => <>
                {service && <Tag
                    color={getTagColor(service.id)}


                >{service.label}</Tag>}
                {!service && <Tag color="red">Aucun service</Tag>}
            </>,
        },


        {
            key: 'description-template',
            title: 'Description',
            dataIndex: 'description',
            width: '40%',
            render: (description) => <><span className={`${styles.tableTdText}`}>{description}</span></>,
            className: `${styles.tableTd}`,

        },
        {
            key: 'type-template',
            title: 'Type',
            dataIndex: 'type',
            render: (type) => <>
            {type === "direction" &&(`Direction`)}
            {type === "service" &&(`Service`)}
            {type === "ads" &&(`Publicités`)}
            {type === "other" &&(`Autre`)}

            </>,
            filters: [
                {text: 'direction', value: 'direction'},
                {text: 'service', value: 'service'},
                {text: 'Publicités', value: 'ads'},
                {text: 'Autre', value: 'Autre'},
            ],
            width: '10%',
        },
        {
            key: 'variables-template',
            title: 'Variables',
            dataIndex: 'variables',
            render: (variables) => <>
                {variables && <Tag color="green">{variables.length}</Tag>}
                {!variables && <Tag color="red">Aucune variable</Tag>}
            </>,
        },
        {
            title: 'Action',
            key: 'action-template',
            render: (_, record) => (
                <>
                    <Space size="middle">

                        <Button onClick={() => {
                            selectTab("2");
                            selectTemplate(record.id);
                            console.log(record, "record");
                        }} className={`${styles.updateStoreArrayBtn}`} icon={<EditOutlined/>} size={"middle"}>
                            Modifier
                        </Button>


                        <Button disabled={record.required} className={`${styles.deleteStoreArrayBtn}`} icon={<DeleteOutlined/>} size={"middle"}>

                        </Button>



                    </Space>
                </>
            ),
        },

    ];

    const validateMessages = {
        required: 'Ce champ est obligatoire !',
    };

    const [servicesData, setServicesData] = useState<DataTypeService[]>();
    const [tableServicesParams, setTableServicesParams] = useState<TableParamsServices>({
        pagination: {
            current: 1,
            pageSize: 10,
        },
    });


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
    }, [tableServicesParams]);




    return (
        <div className={`${styles.fullWidthElement}`}>


            <Row className={`${styles.fullWidthElement}`}>
                <Col
                    className={`${styles.fullWidthElement} d-flex justify-content-between align-items-center mt-3 mb-4`}>
                    <h6 className={`${styles.storeAdminDashboardTitle}`}>
                        Gestion des Templates de correspandances
                    </h6>
                    <Button
                        onClick={() => {
                            addNewTemplate();
                        }}
                        className={`${styles.addNewManagerBtn}`}>
                        <PlusOutlined/>
                        Ajouter un nouveau template
                    </Button>
                </Col>

                <Col className={styles.fullWidthElement}>
                    <ConfigProvider locale={frFR}>
                        <Table
                            locale={{emptyText: customEmptyText}}
                            columns={columns}
                            rowKey={(record) => record.id}
                            dataSource={templateArray}
                            pagination={tableParams.pagination}
                            loading={loading}
                            onChange={handleTableChange as any}
                        />
                    </ConfigProvider>
                </Col>
            </Row>





            <Modal title={
                <div className={`${styles.modalTitle}`}>
                    <h6 className={`${styles.modalTitleText}`}>
                        Ajouter un nouveau template
                    </h6>
                </div>
            } open={isModalVisible} onOk={handleOk} onCancel={handleCancel}
                   okText={"Ajouter"}
                   cancelText="Annuler"
            >

                <Form
                    form={formRef}
                    layout="vertical"
                    name="basic"
                    initialValues={{remember: true}}
                    onFinish={() => {
                        handleFormOk();
                    }}
                    onFinishFailed={() => {
                        setCreateEmailTemplateError(true);
                    }}
                    onValuesChange={(changedValues, allValues) => {
                        setFormTemplate((prevData) => ({...prevData, ...changedValues}));
                    }}
                    validateMessages={validateMessages}
                >
                    <Form.Item
                        label="Nom"
                        name="name"
                        help={CreateEmailTemplateError}
                        rules={[{required: true, message: 'Veuillez entrer un titre pour le template'}]}
                        validateStatus={formTemplate.name === "" ? "error" : "success"}
                        hasFeedback
                    >
                        <Input
                            onChange={(e) => {
                                handleFormChange("name", e.target.value);
                            }}
                            value={formTemplate.name}

                        />
                    </Form.Item>

                    <Form.Item
                        label="Titre"
                        name="title"
                        help={CreateEmailTemplateError}
                        rules={[{required: true, message: 'Veuillez entrer un titre pour le template'}]}
                        validateStatus={formTemplate.title === "" ? "error" : "success"}
                        hasFeedback
                    >
                        <Input
                        onChange={(e) => {
                            handleFormChange("title", e.target.value);
                        }}
                        value={formTemplate.title}

                        />
                    </Form.Item>

                    <Form.Item
                        label="Description"
                        name="description"
                        rules={[{required: true, message: 'Veuillez entrer une description pour le template'}]}
                        help={CreateEmailTemplateError}
                        validateStatus={formTemplate.description === "" ? "error" : "success"}
                        hasFeedback
                    >
                        <Input.TextArea
                        onChange={(e) => {
                            handleFormChange("description", e.target.value);
                        }}
                        value={formTemplate.description}
                        />
                    </Form.Item>

                    <Form.Item
                        label="Type"
                        name="type"
                        rules={[{required: true, message: 'Veuillez entrer un type pour le template'}]}
                        help={CreateEmailTemplateError}
                        validateStatus={formTemplate.type === "" ? "error" : "success"}
                        hasFeedback
                    >
                        <Select
                        onChange={(e) => {
                            handleFormChange("type", e);
                        }}
                        value={formTemplate.type}
                        >
                            <Select.Option value="direction">Direction</Select.Option>
                            <Select.Option value="service">Service</Select.Option>
                            <Select.Option value="ads">Publicités</Select.Option>
                            <Select.Option value="other">Autre</Select.Option>
                        </Select>
                    </Form.Item>

                    <Form.Item
                        label="Service"
                        name="service"
                        rules={[{required: true, message: 'Veuillez entrer un service pour le template'}]}
                        help={CreateEmailTemplateError}
                        validateStatus={formTemplate.service === "" ? "error" : "success"}
                        hasFeedback
                    >
                        <Select

                        onChange={(e) => {
                            handleFormChange("service", e);
                        }}
                        value={formTemplate.service}
                        >
                            {servicesData?.map((service) => (
                                <Select.Option value={service.id}>{service.label}</Select.Option>

                            ))}

                        </Select>
                    </Form.Item>


                </Form>

            </Modal>
        </div>
    );
}

export default SettingsCorrespandancesTemplates;