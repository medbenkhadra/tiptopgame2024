import React, {useState} from 'react'
import {Col, Row} from 'react-bootstrap';

import type {DatePickerProps} from 'antd';
import {Button, ConfigProvider, DatePicker, Form, Input, Modal, Select, Space} from 'antd';


import '../../../app/globals.css'
import {
    ArrowLeftOutlined,
    EyeInvisibleOutlined,
    EyeOutlined,
    FacebookFilled,
    GoogleSquareFilled,
    LockOutlined,
    MailOutlined,
    PhoneOutlined,
    UserAddOutlined,
    UserOutlined
} from '@ant-design/icons';
import 'dayjs/locale/fr';
import locale from 'antd/locale/fr_FR';


import styles from '../../../styles/pages/auth/clientRegisterPage.module.css';
import {register} from '@/app/api';
import Image from "next/image";
import logoTipTopImg from "@/assets/images/tipTopLogoAux.png";

type registerUserForm = {
    email?: string;
    password?: string;
    firstname?: string;
    lastname?: string;
    gender?: string;
    phone?: string;
    passwordConfirm?: string;
    dateOfBirth?: string;
}

const userFormData = {
    firstname: '',
    lastname: '',
    email: '',
    dateOfBirth: "",
    password: "",
    phone: '',
    passwordConfirm: "",
    gender: "",
    role: "ROLE_CLIENT",
};

const {Option} = Select;


type Props = {
    formStep: number;
    handleFormStepChange: () => void;
};

const dateFormat = 'DD/MM/YYYY';

const {RangePicker} = DatePicker;


const GOOGLE_CLIENT_ID = process.env.NEXT_PUBLIC_GOOGLE_CLIENT_ID || '';
const GOOGLE_CLIENT_SECRET = process.env.NEXT_PUBLIC_GOOGLE_CLIENT_SECRET || '';
const GOOGLE_AUTHORIZATION_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
const GOOGLE_REDIRECT_URL = process.env.NEXT_PUBLIC_GOOGLE_REDIRECT_URI_DEV || '';



export default function RegisterClientForm({formStep, handleFormStepChange}: Props) {


    const [formRef] = Form.useForm();
    const [userForm, setUserForm] = useState(userFormData);
    const [loadingButton, setLoadingButton] = useState(false);



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


    const userFormHandleChange = (e: React.ChangeEvent<HTMLInputElement>, ch: string) => {
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


    const [emailExists, setEmailExists] = useState(false);

    const [passwordErrorExists, setPasswordErrorExists] = useState(false);
    function registerClient() {
        if (userForm.firstname == "" || userForm.lastname=="" || userForm.email=="" || userForm.dateOfBirth==""
        || userForm.phone=="" || userForm.gender=="" || userForm.password=="" || userForm.passwordConfirm==""
        ) {
            Modal.error({
                className: 'antdLoginRegisterModal',
                title: 'Un problème est survenu !',
                content: <>
                    <span>Veuillez remplir tous les champs.</span> <br/>
                </>,
                okText: "D'accord",
            });
        }


        if (userForm.email == "") {
            setEmailExists(true);
            console.log("email is empty");
            return;
        }

            if (userForm.firstname == "") {
            console.log("firstname is empty");
            return;
        }
        if (userForm.lastname == "") {
            console.log("lastname is empty");
            return;
        }

        if (userForm.dateOfBirth == "") {
            console.log("dateOfBirth is empty");
            return;
        }
        if (userForm.phone == "") {
            console.log("phone is empty");
            return;
        }

        if (userForm.gender == "") {
            console.log("gender is empty");
            return;
        }
        if (userForm.password == "" || userForm.passwordConfirm == "") {
            return;
        }

        if (userForm.password !== userForm.passwordConfirm ) {
            setEmailExists(false);
            setPasswordErrorExists(true)
            console.log("password not match");
            Modal.error({
                className: 'antdLoginRegisterModal',
                title: 'Mot de passe non identique !',
                content: <>
                    <span> Veuillez vérifier les mots de passe saisis. </span>
                </>,
                okText: "D'accord",
            });
            return;
        }else {
            setPasswordErrorExists(false)
        }

        setLoadingButton(true);

        register(userForm).then((response) => {
            setLoadingButton(false);
            console.log(response.status);
            if (response.status === "success") {
                setEmailExists(false);
                formRef.resetFields();
                Modal.success({
                    className: 'modalSuccess antdLoginRegisterModal',
                    title : 'Inscription réussie !',
                    content: <>
                        <strong>Vous êtes inscrit avec succès.</strong> <br/>
                        <span>Vous pouvez maintenant vous connecter.</span>
                    </>,
                    okText: "Se connecter",
                    onOk() {
                        handleFormStepChange();
                    }
                });
            }
        }).catch((err) => {
            setLoadingButton(false);
            console.log(err);
            if (err.response) {
                if (err.response.status === 400) {
                    console.log(err.response.data.error);
                    if(err.response.data.error == "Email already registered"){
                        setEmailExists(true);
                        Modal.error({
                            className: 'antdLoginRegisterModal',
                            title: 'Un problème est survenu !',
                            content: <>
                                <span>Un compte avec cet email existe déjà.</span> <br/>
                                <span>Si vous n'avez pas de compte, veuillez vous inscrire avec un autre email.</span>
                            </>,
                            okText: "D'accord",
                        });
                    }
                }else{
                    Modal.error({
                        className: 'antdLoginRegisterModal',
                        title: 'Un problème est survenu !',
                        content: <>
                            <span>Un problème est survenu lors de l'inscription.</span> <br/>
                            <span>Veuillez réessayer plus tard.</span>
                        </>,
                        okText: "D'accord",
                    });
                }
            } else {
                console.log(err.request);
            }
        })
    }

    const [passwordVisible, setPasswordVisible] = useState(false);
    const togglePasswordVisibility = () => {
        setPasswordVisible(!passwordVisible);
    };

    const googleCallBackHandle = () => {
        console.log('googleCallBackHandle');
        console.log(GOOGLE_CLIENT_ID);
        console.log(GOOGLE_CLIENT_SECRET);
        console.log(GOOGLE_AUTHORIZATION_URL);
        let url = GOOGLE_AUTHORIZATION_URL;
        url += '?client_id=' + GOOGLE_CLIENT_ID;
        url += '&redirect_uri=' + encodeURIComponent(GOOGLE_REDIRECT_URL);
        url += '&response_type=code';
        url += '&scope=openid profile email';
        window.location.href = url;
    }

    return (
        <div className={`${styles.loginForm} `}>
            <Row className={`${styles.loginFormTopHeader} p-0 m-0`}>

                <Col>
                    <h1> <a href="/">
                        <ArrowLeftOutlined className={`${styles.leftArrowIcon}`}/>
                    </a>Inscrivez-vous<UserAddOutlined className={`${styles.loginIcon}`}/></h1>
                </Col>


            </Row>
            <Row className={`${styles.loginLogo} p-0 m-0`}>
                <a className={`${styles.loginLogo} p-0 m-0`} href="/">
                    <Image
                        src={logoTipTopImg}
                        alt="Picture of the author"
                    >

                    </Image>
                </a>
            </Row>

            <Row className={`mt-5 px-md-5 px-sm-1  d-flex justify-content-center `}>
                <Form
                    name="basic"
                    labelCol={{span: 8}}
                    wrapperCol={{span: 24}}
                    initialValues={{remember: +true}}
                    onFinish={onFinish}
                    onFinishFailed={onFinishFailed}
                    autoComplete="off"
                    validateMessages={validateMessages}
                    className={`${styles.registerForm}`}
                >
                    <Space.Compact>
                        <Form.Item<registerUserForm>
                            name="lastname"
                            rules={[{required: true, message: validateMessages['required']}]}
                            className={`${styles.antdLoginInputs}`}
                        >
                            <Input onChange={(e) => {
                                userFormHandleChange(e, "lastname");
                            }}
                                   placeholder='Nom' className={`${styles.inputsLoginPage}`}
                                   prefix={<UserOutlined className={`${styles.inputsLoginPageIcons}`}/>}/>
                        </Form.Item>
                        <Form.Item<registerUserForm>
                            name="firstname"
                            rules={[{required: true, message: validateMessages['required']}]}
                            className={`${styles.antdLoginInputs}`}
                        >
                            <Input onChange={(e) => {
                                userFormHandleChange(e, "firstname");
                            }} placeholder='Prénom' className={`${styles.inputsLoginPage}`}
                                   prefix={<UserOutlined className={`${styles.inputsLoginPageIcons}`}/>}/>
                        </Form.Item>
                    </Space.Compact>


                        <Space.Compact>
                            <Form.Item<registerUserForm>
                                name="email"
                                rules={[{required: true, message: validateMessages['required']}]}
                                className={`${styles.antdLoginInputs}`}
                                validateStatus={emailExists ? 'error' : ''}
                            >
                                <Input onChange={(e) => {
                                    userFormHandleChange(e, "email");
                                }} placeholder='E-mail' type='email' className={`${styles.inputsLoginPage}`}
                                       prefix={<MailOutlined className={`${styles.inputsLoginPageIcons}`}/>}/>
                            </Form.Item>

                            <Form.Item<registerUserForm>
                                name="dateOfBirth"
                                rules={[{
                                    required: userForm.dateOfBirth == "",
                                    message: 'La date de naissance est requise.'
                                }]}
                                validateStatus={userForm.dateOfBirth == "" ? 'error' : emailExists ? 'error' : ''}
                            >
                                <ConfigProvider locale={locale}>
                                    <DatePicker
                                        onChange={handleDateChange}
                                        format={dateFormat}
                                        className={`${styles.inputsLoginPage}`} renderExtraFooter={() =>
                                        <>
                                            <span className='mx-4'>Date de naissance</span>
                                        </>
                                    }
                                        placeholder='Date de naissance'
                                    />
                                </ConfigProvider>
                            </Form.Item>
                        </Space.Compact>



                    <Space.Compact>
                        <Form.Item<registerUserForm>
                            name="phone"
                            rules={[{required: true, message: validateMessages['required']}]}
                            className={`${styles.antdLoginInputs}`}
                        >
                            <Input
                                onChange={(e) => {
                                    userFormHandleChange(e, "phone");
                                }}
                                placeholder='Numéro de téléphone' className={`${styles.inputsLoginPage}`}
                                prefix={<PhoneOutlined className={`${styles.inputsLoginPageIcons}`}/>}/>
                        </Form.Item>
                        <Form.Item<registerUserForm>
                            name="gender"
                            rules={[{required: true, message: validateMessages['required']}]}
                            className={`${styles.antdLoginInputs}`}
                            validateStatus={userForm.gender=="" ? 'error' : ''}
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
                    </Space.Compact>


                    <Space.Compact>
                        <Form.Item<registerUserForm>
                            name="password"
                            rules={[{required: true, message: validateMessages['required']}]}
                            className={`${styles.antdLoginInputs}`}
                            validateStatus={passwordErrorExists ? 'error' : ''}
                        >
                            <Input onChange={(e) => {
                                userFormHandleChange(e, "password");
                            }} className={`${styles.inputsLoginPage}`}
                                            prefix={<LockOutlined className={`${styles.inputsLoginPageIcons}`}/>}
                                            placeholder='Mot de passe'
                                            suffix={
                                                <div className={`${styles.eyePasswordIcon}`}>
                                                    <span className={`${styles.eyePasswordIconRow}`}>{passwordVisible ?
                                                        <EyeOutlined onClick={togglePasswordVisibility} className={`${styles.inputsLoginPageIcons}`}/> :
                                                        <EyeInvisibleOutlined onClick={togglePasswordVisibility}
                                                                              className={`${styles.inputsLoginPageIcons}`}/>}</span>
                                                </div>
                                            }
                                   type={passwordVisible ? 'text' : 'password'}

                            />
                        </Form.Item>
                        <Form.Item<registerUserForm>
                            name="passwordConfirm"
                            rules={[{required: true, message: validateMessages['required']}]}
                            className={`${styles.antdLoginInputs}`}
                            validateStatus={passwordErrorExists ? 'error' : ''}
                        >

                            <Input onChange={(e) => {
                                userFormHandleChange(e, "passwordConfirm");
                            }} className={`${styles.inputsLoginPage}`}
                                            prefix={<LockOutlined className={`${styles.inputsLoginPageIcons}`}/>}
                                            placeholder='Répétez le mot de passe'
                                            type={passwordVisible ? 'text' : 'password'}
                                   suffix={
                                       <div className={`${styles.eyePasswordIcon}`}>
                                                    <span className={`${styles.eyePasswordIconRow}`}>{passwordVisible ?
                                                        <EyeOutlined onClick={togglePasswordVisibility} className={`${styles.inputsLoginPageIcons}`}/> :
                                                        <EyeInvisibleOutlined onClick={togglePasswordVisibility}
                                                                              className={`${styles.inputsLoginPageIcons}`}/>}</span>
                                       </div>
                                   }


                            />
                        </Form.Item>
                    </Space.Compact>
                    <Form.Item className={`pt-3`}>
                        <Button
                            loading={loadingButton}
                            disabled={loadingButton}
                            onClick={() => {
                                registerClient();
                            }}
                            className={`w-100 ${styles.loginBtnSubmit}`} type="primary" htmlType="submit">
                            Valider l'inscription
                        </Button>
                    </Form.Item>
                </Form>

                <Row>

                    <Col>
                        <a href="#" onClick={() => {
                            handleFormStepChange();
                        }} className={`${styles.resetPasswordLink}`}><UserAddOutlined
                            className={`${styles.resetPasswordIcon}`}/>
                            Vous avez déjà un compte ? Connectez-vous ici
                            !</a>
                    </Col>
                </Row>

                <Col>
                    <Row className={`mt-3  px-sm-2 px-md-5 d-flex justify-content-center `}>
                        <div className={`${styles.divider}`}>
                            <div className={`${styles['divider-text']}`}>Ou</div>
                        </div>
                        <div className={`pt-3`}>
                            <p className={`text-center`}>
                                Inscrivez-vous avec votre compte Google.
                            </p>
                        </div>


                        <div className={`pb-3  d-flex justify-content-center `}>
                            <Space direction="vertical" style={{ width: '100%' }}>
                                <Button onClick={() => {
                                    googleCallBackHandle();
                                }} className={`${styles.googleLoginBtn}`} icon={<GoogleSquareFilled />} block>
                                    <span><small>Se connecter avec Google</small></span>
                                </Button>
                            </Space>
                        </div>
                    </Row>
                </Col>
            </Row>

            <Row>
                <Col>
                    <div className={`${styles.navLinkLogin} d-flex`}>
                        <p>&copy; 2024 Furious Ducks. Tous droits réservés.</p>
                    </div>
                </Col>
            </Row>
        </div>
    )
}
