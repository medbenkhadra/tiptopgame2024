import React, {useEffect, useState} from 'react';
import {ColumnsType, TablePaginationConfig} from "antd/es/table";
import {FilterValue, SorterResult} from "antd/es/table/interface";
import {
    Button,
    Col,
    ConfigProvider,
    DatePicker,
    DatePickerProps,
    Form,
    Input, message,
    Row,
    Select, Space, Switch,
    Table,
    Tag
} from "antd";
import styles from "@/styles/pages/dashboards/storeAdminDashboard.module.css";
import {addNewUserForStore, getStoreUsersByRoleAndStoreId, getUserProdileById, updateUserById} from "@/app/api";
import LogoutService from "@/app/service/LogoutService";
import {
    DeleteOutlined,
    DownloadOutlined, EditOutlined,
    LockOutlined,
    MailOutlined,
    PhoneOutlined, PlusCircleOutlined, PlusOutlined, StopOutlined,
    UserOutlined
} from "@ant-design/icons";
import {Modal} from 'antd';
import locale from "antd/locale/fr_FR";
import dayjs from "dayjs";
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

interface TableParams {
    pagination?: TablePaginationConfig;
    sortField?: string;
    sortOrder?: string;
    filters?: Record<string, FilterValue>;
    role?: string;
}


type managerUserForm = {
    id?: string;
    email?: string;
    firstname?: string;
    lastname?: string;
    gender?: string;
    phone?: string;
    dateOfBirth?: string;
    role: string;
    status?: string;
}

const managerUserFormData = {
    id: "",
    firstname: '',
    lastname: '',
    email: '',
    dateOfBirth: "",
    phone: '',
    gender: "",
    role: "",
    status: "",
};
const dateFormat = 'DD/MM/YYYY';
const {Option} = Select;


interface storeManagersTableProps {
    selectedStoreId: string;
    profilesRole: string;
    roleKey: string;
    onUpdate: () => void;
}

function StoreManagerTable({selectedStoreId, profilesRole,roleKey , onUpdate}: storeManagersTableProps) {



    const {logoutAndRedirectAdminsUserToLoginPage} = LogoutService();
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [formRef] = Form.useForm();
    const showModal = () => {
        setIsModalOpen(true);
    };


    const handleCancel = () => {
        setIsModalOpen(false);
    };

    const columns: ColumnsType<DataType> = [
        {
            title: 'Nom',
            dataIndex: 'lastname',
            sorter: true,
            render: (lastname) => `${lastname}`,
            className: `${styles.lastnameColProfileManagement}`,

        },
        {
            title: 'Prénom',
            dataIndex: 'firstname',
            sorter: true,
            render: (firstname) => `${firstname}`,
            className: `${styles.firstnameColProfileManagement}`,
        },
        {
            title: 'Email',
            dataIndex: 'email',
        },
        {
            title: 'Genre',
            dataIndex: 'gender',
            className: `${styles.genderColProfileManagement}`,
            filters: [
                {text: 'Homme', value: 'Homme'},
                {text: 'Femme', value: 'Femme'},
                {text: 'Autre', value: 'Autre'},
            ],
            width: '20%',
        },
        {
            title: 'Age',
            dataIndex: 'age',
            sorter: true,
        },
        {
            title: 'Téléphone',
            dataIndex: 'phone',
        },
        {
            title: 'Status',
            dataIndex: 'status',
            filters: [
                {text: 'Ouvert', value: '1'},
                {text: 'Fermé', value: '2'},
            ],
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
                            resetPassword(record.id);
                        }} className={`${styles.resetPasswordArrayBtn}`}
                                icon={<LockOutlined/>} size={"middle"}>
                             MPD
                        </Button>
                        <Button onClick={() => {
                            updateManager(record.id);
                        }} className={`${styles.updateStoreArrayBtn}`} icon={<EditOutlined/>} size={"middle"}>
                            Modifier
                        </Button>
                        <Button className={`${styles.deleteStoreArrayBtn}`} icon={<DeleteOutlined/>}
                                size={"middle"}></Button>
                    </Space>
                </>
            ),
        },

    ];


    const [data, setData] = useState<DataType[]>();
    const [loading, setLoading] = useState(false);
    const [tableParams, setTableParams] = useState<TableParams>({
        pagination: {
            current: 1,
            pageSize: 10,
        },
    });



    useEffect(() => {
        if(roleKey=="1") {
            setUserForm((prevFormData) => ({
                ...prevFormData,
                role: "ROLE_STOREMANAGER",
            }));
        }else if(roleKey=="2") {
            setUserForm((prevFormData) => ({
                ...prevFormData,
                role: "ROLE_EMPLOYEE",
            }));
        }else if(roleKey=="3") {
            setUserForm((prevFormData) => ({
                ...prevFormData,
                role: "ROLE_CLIENT",
            }));
        }
    }, [roleKey]);

    function fetchData() {
        setLoading(true);
        getStoreUsersByRoleAndStoreId(selectedStoreId, tableParams , roleKey).then((response) => {
            console.log('response : ', response);
            setData(response.storeManagerUsers);
            setTableParams({
                ...tableParams,
                pagination: {
                    ...tableParams.pagination,
                    total: response.totalCount,
                },
            });
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
        if (selectedStoreId != '' && selectedStoreId != null && !loading ) {
            fetchData();
        }
    }, [JSON.stringify(tableParams), selectedStoreId]);


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


    const [userForm, setUserForm] = useState<managerUserForm>(managerUserFormData);
    const [updateManagerModal, setUpdateManagerModal] = useState(false);

    function addNewManager() {
        setUserForm((managerUserFormDataPrev: any) => ({
            ...managerUserFormDataPrev,
            role: profilesRole,
        }));

        setUpdateManagerModal(false);
        showModal();
    }

    const onFinish = (values: any) => {
        console.log('Success:', values);
    };

    const onFinishFailed = (errorInfo: any) => {
        console.log('Failed:', errorInfo);
    };

    const validateMessages = {
        required: 'Ce champ est obligatoire !',
    };


    const handleDateChange: DatePickerProps['onChange'] = (date, dateString) => {
        console.log(date, dateString);
        if (dateString && date) {
            console.log(date.format('DD/MM/YYYY'));
            let ch = date.format('DD/MM/YYYY');
            setUserForm((prevFormData) => ({
                ...prevFormData,
                dateOfBirth: ch,
            }));
        }
    };


    const managerUserFormHandleChange = (e: React.ChangeEvent<HTMLInputElement>, ch: string) => {
        let inputValue = e.target.value;
        setUserForm((prevFormData) => ({
            ...prevFormData,
            [ch]: inputValue,
        }));
    }

    const userGenderFormHandleChange = (value: any) => {
        setUserForm((prevFormData) => ({
            ...prevFormData,
            gender: value,
        }));
    }

    const userFormHandleChangeStatus = (e: boolean) => {
        setUserForm((prevFormData) => ({
            ...prevFormData,
            status: e ? "1" : "2",
        }));
    }


    async function updateUserProfile(id: string) {
        try {
            const response = await updateUserById(id, userForm);
            onUpdate();
            return "updated";
        } catch (err:any) {
            if (err.response) {
                if (err.response.status === 401) {
                    logoutAndRedirectAdminsUserToLoginPage();
                } else if (err.response.status === 400) {
                    setEmailExists(true);
                    return "error 400";
                }
            } else {
                console.log(err.request);
            }
            return "error";
        }
    }



    function updateManager(id: string) {
        setEmailExists(false);
        formRef.resetFields();
        setUpdateManagerModal(true);
        getUserProdileById(id).then(async (response) => {
            console.log('getUserProdileById response : ', response);
                setUserForm({
                id: response.user.id,
                firstname: response.user.firstname,
                lastname: response.user.lastname,
                email: response.user.email,
                dateOfBirth: response.user.dateOfBirth,
                phone: response.user.phone,
                status: response.user.status,
                gender: response.user.gender,
                role: response.user.role,
            });
            formRef.setFieldsValue({
                id: response.user.id,
                firstname: response.user.firstname,
                lastname: response.user.lastname,
                email: response.user.email,
                dateOfBirth: response.user.dateOfBirth,
                phone: response.user.phone,
                status: response.user.status,
                gender: response.user.gender,
                role: response.user.role,
            });

            showModal();
        }).catch((err) => {
            console.log('getUserProdileById err : ', err);
        })
    }

    function resetPassword(id: string) {
        console.log('resetPassword');
    }


    const [emailExists, setEmailExists] = useState(false);
    const handleOk = async () => {
        if (updateManagerModal) {
            try {
                message.destroy();
                await formRef.validateFields();
                const updateUserResponse = await updateUserProfile(userForm.id ? userForm.id : "") as any;
                if(updateUserResponse=="updated"){
                    message.success('L\'utilisateur a été bien modifié avec succés');
                    formRef.resetFields();
                    setUserForm(managerUserFormData);
                    setEmailExists(false);
                    setIsModalOpen(false);
                    fetchData();
                }else if (updateUserResponse=="error 400"){
                    setEmailExists(true);
                    message.error(
                        <>
                            Veuillez choisir une autre adresse e-mail <br/>
                            <strong> Adresse e-mail déjà utilisée </strong>
                        </>
                    );
                }else {
                    message.error(
                        <>
                            Un problème est survenu lors de l'ajout d'un nouveau manager <br/>
                            <strong>{updateUserResponse}</strong>
                        </>
                    );
                }
            } catch (e: any) {
                console.log("eeeeeee : ", e.errorFields[0].errors[0]);
                message.error(
                    <>
                        Un problème est survenu lors de la modification du manager <br/>
                        <strong>{e.errorFields[0].errors[0]}</strong>
                    </>
                );

            }
        } else {
            try {
                message.destroy();
                await formRef.validateFields();
                addNewUserForStore(selectedStoreId, userForm).then((response) => {
                    onUpdate();
                    message.success('L\'utlisateur a été bien ajouté avec succés');
                    formRef.resetFields();
                    setUserForm(managerUserFormData);
                    setEmailExists(false);
                    setIsModalOpen(false);
                    fetchData();
                }).catch((err: any) => {
                    if (err.response) {
                        if (err.response.status === 401) {
                            logoutAndRedirectAdminsUserToLoginPage();
                        } else if (err.response.status === 400) {
                            setEmailExists(true);
                            message.error(
                                <>
                                    Veuillez choisir une autre adresse e-mail <br/>
                                    <strong> Adresse e-mail déjà utilisée </strong>
                                </>
                            );
                        }
                    } else {
                        message.error(
                            <>
                                Un problème est survenu lors de l'ajout d'un nouveau manager : <br/>
                                <strong>Veuillez vérifier les informations saisies</strong>
                            </>
                        );
                    }
                })
            } catch (e: any) {
                console.log("eeeeeee : ", e.errorFields[0].errors[0]);
                message.error(
                    <>
                        Un problème est survenu lors de l'ajout d'un nouveau manager <br/>
                        <strong>{e.errorFields[0].errors[0]}</strong>
                    </>
                );

            }
        }
    };





    const customEmptyText = (
        <div className={styles.emptyTableTextDiv}>
            <span>Aucun
                {profilesRole == "ROLE_STOREMANAGER" && ' manager'} {profilesRole == "ROLE_EMPLOYEE" && ' employé'} {profilesRole == "ROLE_CLIENT" && ' client'} trouvé
            </span>
            <span><StopOutlined /></span>
        </div>
    );


    return (
        <>
            <Row className={`${styles.fullWidthElement}`}>
                <Col className={`${styles.fullWidthElement} ${styles.addNewManagerBtnDiv}`}>
                    {(profilesRole == "ROLE_STOREMANAGER" || profilesRole=="ROLE_EMPLOYEE") && (
                        <Button onClick={addNewManager} className={`${styles.addNewManagerBtn}`} icon={<PlusOutlined/>}
                                size={"large"}>
                            {profilesRole == "ROLE_STOREMANAGER" && 'Ajouter un nouveau manager'} {profilesRole == "ROLE_EMPLOYEE" && 'Ajouter un nouveau employé'}
                        </Button>
                    )}

                </Col>
                <Col className={styles.fullWidthElement}>
                    <ConfigProvider locale={frFR}>
                    <Table
                        locale={{emptyText : customEmptyText}}
                        columns={columns}
                        rowKey={(record) => record.id}
                        dataSource={data}
                        pagination={tableParams.pagination}
                        loading={loading}
                        onChange={handleTableChange as any}
                    />
                    </ConfigProvider>
                </Col>
            </Row>


            {/* Modal to add new  manager or update existing one */}
            <Modal title={
                updateManagerModal ?
                    <>
                        {profilesRole == "ROLE_STOREMANAGER" && 'Modifier le manager'} {profilesRole == "ROLE_EMPLOYE" && 'Modifier l\'employé'} {profilesRole == "ROLE_CLIENT" && 'Modifier le client'}
                    </>
                    :
                    <>
                        {profilesRole == "ROLE_STOREMANAGER" && 'Ajouter un nouveau manager'} {profilesRole == "ROLE_EMPLOYE" && 'Ajouter un nouveau employé'} {profilesRole == "ROLE_CLIENT" && 'Ajouter un nouveau client'}
                    </>
            } open={isModalOpen} onOk={handleOk} onCancel={handleCancel}
                   okText={updateManagerModal ? "Modifier" : "Ajouter"}
                   cancelText="Annuler"
            >
                <Form
                    name="basic"
                    labelCol={{span: 8}}
                    wrapperCol={{span: 24}}
                    initialValues={{remember: +true}}
                    onFinish={onFinish}
                    onFinishFailed={onFinishFailed}
                    autoComplete="off"
                    validateMessages={validateMessages}
                    className={`${styles.fullWidthElement}`}
                    form={formRef}
                >
                    <Form.Item<managerUserForm>
                        name="lastname"
                        rules={[{
                            required: true,
                            message: "Le nom est obligatoire"
                        }]}
                        className={`${styles.fullWidthElement}`}
                    >
                        <Input onChange={(e) => {
                            managerUserFormHandleChange(e, "lastname");
                        }}
                               placeholder='Nom' className={`${styles.inputsLoginPage}`}
                               prefix={<UserOutlined className={`${styles.inputsLoginPageIcons}`}/>}/>
                    </Form.Item>
                    <Form.Item<managerUserForm>
                        name="firstname"
                        rules={[{
                            required: true,
                            message: "Le prénom est obligatoire"
                        }]}
                        className={`${styles.antdLoginInputs}`}
                    >
                        <Input onChange={(e) => {
                            managerUserFormHandleChange(e, "firstname");
                        }} placeholder='Prénom' className={`${styles.inputsLoginPage}`}
                               prefix={<UserOutlined className={`${styles.inputsLoginPageIcons}`}/>}/>
                    </Form.Item>


                    <Form.Item<managerUserForm>
                        name="email"
                        rules={[{
                            required: true,
                            message: 'L\'email est obligatoire.'
                        }]}
                        className={`${styles.antdLoginInputs}`}
                        validateStatus={emailExists ? 'error' : ''}
                    >
                        <Input onChange={(e) => {
                            managerUserFormHandleChange(e, "email");
                        }} placeholder='E-mail' type='email' className={`${styles.inputsLoginPage}`}
                               prefix={<MailOutlined className={`${styles.inputsLoginPageIcons}`}/>}/>
                    </Form.Item>

                    <Form.Item<managerUserForm>
                        name="dateOfBirth"

                        className={`${styles.fullWidthElement}`}
                        rules={[{
                            required: userForm.dateOfBirth == "",
                            message: 'La date de naissance est obligatoire.'
                        }]}
                    >
                        <ConfigProvider locale={locale}>
                            <DatePicker
                                onChange={handleDateChange}
                                format={dateFormat}
                                className={`${styles.fullWidthElement}`}
                                value={userForm.dateOfBirth ? dayjs(userForm.dateOfBirth, dateFormat) : null}
                                renderExtraFooter={() =>
                                    <>
                                        <span className='mx-4'>Date de naissance</span>
                                    </>
                                }
                                placeholder='Date de naissance'
                            />
                        </ConfigProvider>
                    </Form.Item>


                    <Form.Item<managerUserForm>
                        name="phone"
                        rules={[{required: true, message: "Numéro de téléphone est obligatoire"}]}
                        className={`${styles.antdLoginInputs}`}
                    >
                        <Input
                            onKeyDown={(event) => {
                                const re = /^[0-9\b+]+$/;
                                if ((!re.test(event.key) && event.key !== 'Backspace')) {
                                    event.preventDefault();
                                }
                                if (event.currentTarget.value.length > 13 && event.key !== 'Backspace') {
                                    event.preventDefault();
                                }
                            }}
                            onChange={(e) => {
                                managerUserFormHandleChange(e, "phone");
                            }}
                            placeholder='Numéro de téléphone' className={`${styles.inputsLoginPage}`}
                            prefix={<PhoneOutlined className={`${styles.inputsLoginPageIcons}`}/>}/>
                    </Form.Item>
                    <Form.Item<managerUserForm>
                        name="gender"
                        rules={[{required: true, message: 'Sexe est obligatoire'}]}
                        className={`${styles.antdLoginInputs}`}
                    >
                        <Select onChange={(value) => {
                            userGenderFormHandleChange(value);
                        }}
                                placeholder="Sélectionnez votre genre" className={`${styles.inputsLoginPage}`}>
                            <Option value="Homme">Homme</Option>
                            <Option value="Femme">Femme</Option>
                            <Option value="Autre">Autre</Option>
                        </Select>
                    </Form.Item>


                    {updateManagerModal && (
                        <>
                            <Form.Item label="Statut du compte (O/F)" name={"status"}>
                                <Switch checked={userForm.status == "1"} onChange={(e: boolean) => {
                                    userFormHandleChangeStatus(e);
                                }}/>
                            </Form.Item>
                        </>
                    )}

                </Form>
            </Modal>
        </>


    );
}

export default StoreManagerTable;