"use client"
import React, {useState, useEffect} from 'react'
import {Container, Row, Col} from 'react-bootstrap';
import RedirectService from '../../../app/service/RedirectService';

import {Button, message, Modal, Space} from 'antd';

import Nav from 'react-bootstrap/Nav';
import Navbar from 'react-bootstrap/Navbar';
import SpinnigLoader from "@/app/components/widgets/SpinnigLoader";
import '../../../app/globals.css'
import {ArrowLeftOutlined, LockOutlined, UserOutlined} from '@ant-design/icons';

import styles from '../../../styles/pages/auth/adminsLoginPage.module.css';
import Icon, {
    LoginOutlined,
    EyeOutlined,
    EyeInvisibleOutlined,
    MailOutlined,
    AppstoreFilled,
    ExclamationCircleOutlined,
} from '@ant-design/icons';

type FieldType = {
    email?: string;
    password?: string;
    remember?: string;
}
import {Checkbox, Form, Input} from 'antd';

type Props = {
    formStep: number;
    setFormStep: React.Dispatch<React.SetStateAction<number>>;
};

const userFormData = {
    email: '',
    password: "",
    remember: "",
};


import {loginAdmin} from '@/app/api';
import Image from "next/image";
import logoTipTopImg from "@/assets/images/logovf.jpeg";



export default function StoreLoginForm() {
    const { redirectAdminToToDashboard } = RedirectService();
    const [loadingButton, setLoadingButton] = useState(false);


    const [loggedInUser, setLoggedInUser] = useState<any>(null);
    useEffect(() => {
        setLoading(true);
        const user = JSON.parse(localStorage.getItem('loggedInUser') || '{}');
        setLoggedInUser(user);
        if (user) {
            setTimeout(() => {
                setLoading(false);
            }, 1000);
        }
        redirectAdminToToDashboard();
    }, []);

    const [loading, setLoading] = useState(true);

    const [userForm, setUserForm] = useState(userFormData);
    const [loginError, setLoginError] = useState(false);



    const onFinish = (values: any) => {
        console.log('Form successfully sent');
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


    function login(formData: FieldType) {
        console.log("formData : ", formData);
        if(formData.email!="" && formData.password!="") {
            setLoadingButton(true);
            //setLoading(true);
            loginAdmin(formData).then((res) => {
                setLoginError(false);
                setLoadingButton(false);
                console.log("res : ", res);
                const loggedInUserToken = res.token;
                const loggedInUser = res.userJson;

                localStorage.setItem("loggedInUserToken", loggedInUserToken);
                localStorage.setItem("loggedInUserId", loggedInUser.id);
                localStorage.setItem("loggedInUserEmail", loggedInUser.email);
                localStorage.setItem("loggedInUserRole", loggedInUser.role);
                localStorage.setItem("loggedInUser", JSON.stringify(loggedInUser));

                redirectAdminToToDashboard();


            }).catch((err) => {
                setLoadingButton(false);
                setLoginError(true);
                Modal.error({
                    className: 'antdLoginRegisterModal',
                    title: 'E-mail ou mot de passe incorrect !',
                    okText: 'Réessayer',
                    content: (
                        <>
                            <p>
                                Veuillez vérifier votre e-mail et votre mot de passe et réessayer.
                            </p>
                        </>
                    ),
                });

                console.log(err);
            });
        }
    }


    return (
<>
    {loading && (
    <>
    <SpinnigLoader></SpinnigLoader>
    </>
        )}
    {!loading && (
            <>
                <Row className={`${styles.loginFormTopHeaderAux} p-0 m-0 pt-0`}>
                    <Col>
                        <Row className={'justify-content-center d-flex align-items-center w-100'}>
                            <Col className={"w-100"}>
                                <h1 className={`${styles.adminsLoginFormtopHeaderText}`}><a href="/">
                                    <ArrowLeftOutlined className={`${styles.leftArrowIcon}`}/>
                                </a> Accès Personnels <LoginOutlined className={`${styles.loginIcon} ${styles.rightArrowIcon}`}/>
                                </h1>
                            </Col>
                        </Row>
                    </Col>


                </Row>
                <div className={`${styles.loginForm} mt-0 pt-0 `}>


                    <Row className={'mt-5 justify-content-center'}>
                        <Col sm={12} md={8} lg={7} xl={5} className={'mt-5 justify-content-center'}>

                            <Row className={`mt-5 px-5 d-flex justify-content-center `}>
                                <Form
                                    name="basic"
                                    labelCol={{span: 8}}
                                    wrapperCol={{span: 24}}
                                    initialValues={{remember: +true}}
                                    onFinish={onFinish}
                                    onFinishFailed={onFinishFailed}
                                    autoComplete="off"
                                    validateMessages={validateMessages}
                                    className={`${styles.loginForm}`}

                                >
                                    <Form.Item<FieldType>
                                        name="email"
                                        rules={[{required: true, message: validateMessages['required']}]}
                                        className={`${styles.antdLoginInputs} ${loginError ? styles.errorInput : ''}`}
                                        validateStatus={loginError ? 'error' : ''}
                                    >
                                        <Input
                                            autoComplete="on"
                                            onChange={(e) => {
                                                userFormHandleChange(e, "email");
                                            }} placeholder='E-mail' className={`${styles.inputsLoginPage}`}
                                            prefix={<UserOutlined className={`${styles.inputsLoginPageIcons}`}/>}/>
                                    </Form.Item>

                                    <Form.Item<FieldType>
                                        name="password"
                                        rules={[{required: true, message: validateMessages['required']}]}
                                        className={`${styles.antdLoginInputs} ${loginError ? styles.errorInput : ''}`}
                                        validateStatus={loginError ? 'error' : ''}


                                    >
                                        <Input.Password
                                            onChange={(e) => {
                                                userFormHandleChange(e, "password");
                                            }}
                                            autoComplete="on"
                                            className={`${styles.inputsLoginPage}`}
                                            prefix={<LockOutlined className={`${styles.inputsLoginPageIcons}`}/>}
                                            placeholder='Mot de passe'
                                            iconRender={(visible) =>
                                                <div className={`${styles.eyePasswordIcon}`}>
                                            <span className={`${styles.eyePasswordIconRow}`}>{visible ?
                                                <EyeOutlined className={`${styles.inputsLoginPageIcons}`}/> :
                                                <EyeInvisibleOutlined
                                                    className={`${styles.inputsLoginPageIcons}`}/>}</span>
                                                </div>
                                            }

                                        />
                                    </Form.Item>

                                    <Row className={`d-flex w-100 justify-content-start  align-items-start flex-column`}>
                                        <Col className={`m-0 py-2 d-flex w-100 justify-content-start  align-items-start flex-column`}>
                                            <Form.Item<FieldType>
                                                name="remember"
                                                className={`m-0 p-0`}
                                            >
                                                <Checkbox className={`${styles.sessionCkeckbox}`}> Garder ma session
                                                    active</Checkbox>
                                            </Form.Item>
                                        </Col>
                                        <Col className={`m-0 py-2 d-flex w-100 justify-content-start  align-items-start flex-column`}>
                                            <a href="/reset_password" className={`${styles.resetPasswordLink}`}>Mot de passe oublié
                                                ? <MailOutlined className={`${styles.resetPasswordIcon}`}/></a>
                                        </Col>

                                    </Row>



                                    <Form.Item className={`py-3 w-100`}>
                                        <Button  loading={loadingButton}
                                                 disabled={loadingButton}
                                                 onClick={() => {
                                            login(userForm);
                                        }} className={`w-100 ${styles.loginBtnSubmit}`} type="primary" htmlType="submit">
                                            Se connecter
                                        </Button>
                                    </Form.Item>
                                </Form>
                            </Row>

                        </Col>
                    </Row>


                    <div className="px-3 py-2 mt-5 pt-5">


                        <Row>
                            <Col>
                                <div className={`${styles.navLinkLogin} d-flex`}>
                                    <p>&copy; 2024 Furious Ducks. Tous droits réservés.</p>
                                </div>
                            </Col>
                        </Row>
                    </div>
                </div>
            </>
    )}
</>
    )
}
