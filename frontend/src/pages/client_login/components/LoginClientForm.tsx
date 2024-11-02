"use client"
import React, {useState} from 'react'
import {Col, Row} from 'react-bootstrap';
import {signIn} from 'next-auth/react';
import Image from 'next/image';
import logoTipTopImg from "@/assets/images/tipTopLogoAux.png";

import {Button, Checkbox, Form, Input, Modal, Space} from 'antd';

import Nav from 'react-bootstrap/Nav';
import Navbar from 'react-bootstrap/Navbar';

import '../../../app/globals.css'
import {
    AppstoreFilled,
    ArrowLeftOutlined,
    ExclamationCircleOutlined,
    EyeInvisibleOutlined,
    EyeOutlined,
    FacebookFilled,
    GoogleSquareFilled,
    LockOutlined,
    LoginOutlined,
    MailOutlined,
    UserAddOutlined,
    UserOutlined
} from '@ant-design/icons';

import styles from '../../../styles/pages/auth/clientLoginPage.module.css';
import {loginClient} from '@/app/api';

type FieldType = {
    email?: string;
    password?: string;
    remember?: string;
}

type Props = {
    formStep: number;
    handleFormStepChange: () => void;
};

const userFormData = {

    email: '',
    password: "",
    remember: "",

};


const GOOGLE_CLIENT_ID = process.env.NEXT_PUBLIC_GOOGLE_CLIENT_ID || '';
const GOOGLE_CLIENT_SECRET = process.env.NEXT_PUBLIC_GOOGLE_CLIENT_SECRET || '';
const GOOGLE_AUTHORIZATION_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
const GOOGLE_REDIRECT_URL = process.env.NEXT_PUBLIC_GOOGLE_REDIRECT_URI_DEV || '';


export default function LoginClientForm({ formStep, handleFormStepChange }: Props) {

    const [userForm, setUserForm] = useState(userFormData);
    const [loginError, setLoginError] = useState<string | null>(null);
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


    const userFormHandleChange = (e: React.ChangeEvent<HTMLInputElement>, ch: string) => {
        let inputValue = e.target.value;
        setUserForm((prevFormData) => ({
            ...prevFormData,
            [ch]: inputValue,
        }));
    }

    const handleClientLogin = async () => {

        if (userForm.email && userForm.password) {
            setLoadingButton(true);
            setLoginError(null);

            loginClient(userForm).then((response) => {
                console.log(response);
                if (response.status === 'success') {
                    setLoadingButton(false);
                    const loggedInUser = response.userJson;
                    localStorage.setItem('loggedInUserToken', response.token);
                    localStorage.setItem('firstLoginClientStatus', response.firstLogin);
                    localStorage.setItem("loggedInUserId", loggedInUser.id);
                    localStorage.setItem("loggedInUserEmail", loggedInUser.email);
                    localStorage.setItem("loggedInUserRole", loggedInUser.role);
                    localStorage.setItem("loggedInUser", JSON.stringify(loggedInUser));
                    window.location.href = '/dashboard/client';
                }
            }).catch((err) => {
                setLoadingButton(false);
                setLoginError('Email ou mot de passe incorrecte !');
                Modal.error({
                    className: 'antdLoginRegisterModal',
                    title: 'Email ou mot de passe incorrecte !',
                    content: <>
                    <span>
                        Veuillez vérifier votre email et mot de passe et réessayer.
                    </span>
                    </>,
                    okText: "D'accord",
                });
            })
        }
    }



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
                    <h1><a href="/">
                        <ArrowLeftOutlined className={`${styles.leftArrowIcon}`}/>
                    </a>
                        Connexion à votre compte <LoginOutlined className={`${styles.loginIcon}`} /></h1>
                </Col>


            </Row>
            <Row className={`${styles.loginLogo} p-0 m-0`}>
                <a className={`${styles.loginLogo} p-0 m-0`} href="/">
                    <Image
                        src={logoTipTopImg}
                        alt="Picture of the logo"
                    >

                    </Image>
                </a>

            </Row>

            <Row className={`mt-5 px-sm-2 px-lg-5 d-flex justify-content-center `}>
                <Form
                    name="basic"
                    labelCol={{ span: 8 }}
                    wrapperCol={{ span: 24 }}
                    initialValues={{ remember: +true }}
                    onFinish={onFinish}
                    onFinishFailed={onFinishFailed}
                    autoComplete="on"
                    validateMessages={validateMessages}
                    className={`${styles.loginForm}`}

                >
                    <Form.Item<FieldType>
                        name="email"
                        rules={[{ required: true, message: validateMessages['required'] }]}
                        className={`${styles.antdLoginInputs}`}
                        validateStatus={loginError ? "error" : "success"}
                        hasFeedback

                    >
                        <Input

                            onChange={(e) => {
                            userFormHandleChange(e, "email");
                        }} placeholder='E-mail' className={`${styles.inputsLoginPage}`} prefix={<UserOutlined className={`${styles.inputsLoginPageIcons}`} />} />
                    </Form.Item>

                    <Form.Item<FieldType>
                        name="password"
                        rules={[{ required: true, message: validateMessages['required'] }]}
                        className={`${styles.antdLoginInputs}`}
                        validateStatus={loginError ? "error" : "success"}
                        help={loginError}
                        hasFeedback
                    >
                        <Input.Password
                            onChange={(e) => {
                                userFormHandleChange(e, "password");
                            }}
                            className={`${styles.inputsLoginPage}`} prefix={<LockOutlined className={`${styles.inputsLoginPageIcons}`} />}
                            placeholder='Mot de passe'
                            iconRender={(visible) =>
                                <div className={`${styles.eyePasswordIcon}`}>
                                    <span className={`${styles.eyePasswordIconRow}`} >{visible ? <EyeOutlined className={`${styles.inputsLoginPageIcons}`} /> : <EyeInvisibleOutlined className={`${styles.inputsLoginPageIcons}`} />}</span>
                                </div>
                            }

                        />
                    </Form.Item>

                    <Row className={`d-flex`}>
                        <Col span={12} md={12} sm={24} className={`m-0 py-2 d-flex justify-content-start`}>
                            <Form.Item<FieldType>
                                name="remember"
                                valuePropName="checked"
                                className={`m-0 p-0`}
                            >
                                <Checkbox className={`${styles.sessionCkeckbox}`}>  Garder ma session active</Checkbox>
                            </Form.Item>
                        </Col>


                        <Col span={12} md={12} sm={24} className={`m-0 py-2 d-flex justify-content-start`}>
                            <a href="/reset_password" className={`${styles.resetPasswordLink}`} >Mot de passe oublié ? <MailOutlined className={`${styles.resetPasswordIcon}`} /></a>
                        </Col>
                    </Row>

                    <Form.Item className={`py-3 w-100`}>
                        <Button
                            loading={loadingButton}
                            disabled={loadingButton}
                            onClick={() => {
                            handleClientLogin();
                        }}  className={`w-100 ${styles.loginBtnSubmit}`} type="primary" htmlType="submit">
                            Se connecter
                        </Button>
                    </Form.Item>
                </Form>

                <Row className={`m-0 p-0`}>

                    <Col className={`m-0 p-0`}>
                        <a href="#" onClick={() => {
                            handleFormStepChange();
                        }} className={`${styles.registerBtn} mb-3`} ><UserAddOutlined className={`${styles.resetPasswordIcon}`} />
                            Rejoignez-nous et gagnez en créant un compte !</a>
                    </Col>
                </Row>

                <Col className={`m-0 p-0`}>
                    <Row className={`m-0 p-0`}>
                        <div className={`${styles.divider}`}>
                            <div className={`${styles['divider-text']}`}>Ou</div>
                        </div>
                        <div className={`pt-2`}>
                            <p className={`text-center`}>
                                Connectez-vous avec votre compte Google.
                            </p>
                        </div>
                        <div className={`py-1`}>
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



            <Row className={`p-0 m-0 mt-4`}>
                <Col>
                    <div className={`${styles.navLinkLogin} d-flex`}>
                        <p>&copy; 2024 Furious Ducks. Tous droits réservés.</p>
                    </div>
                </Col>
            </Row>
        </div>
    )
}
