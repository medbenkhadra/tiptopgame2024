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
} from '@ant-design/icons';

type FieldType = {
    email?: string;
}
import {Checkbox, Form, Input} from 'antd';

type Props = {
    formStep: number;
    setFormStep: React.Dispatch<React.SetStateAction<number>>;
};

const userFormData = {
    email: '',
};


import {resetPasswordRequest} from '@/app/api';

export default function ResetPasswordForm() {
    const [loadingButton, setLoadingButton] = useState(false);
    const [resetPasswordError, setresetPasswordError] = useState(false);

    const [loggedInUser, setLoggedInUser] = useState<any>(null);
    useEffect(() => {

    }, []);

    const [loading, setLoading] = useState(false);

    const [userForm, setUserForm] = useState(userFormData);



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


    function resetPasswordHandler() {
        if(userForm.email === ''){
            setresetPasswordError(true);
            return;
        }

        setLoadingButton(true);

        let emailPattern = new RegExp(/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/);
        if (!emailPattern.test(userForm.email)) {
            setresetPasswordError(true);
            setLoadingButton(false);
            Modal.error({
                title: 'E-mail invalide',
                content: 'Veuillez saisir un e-mail valide',
                onOk() {
                    setUserForm(userFormData);
                }
            });
            return;
        }

        resetPasswordRequest(userForm).then((response) => {
            setLoadingButton(false);
            Modal.success({
                title: 'Lien de réinitialisation envoyé',
                content: 'Un e-mail contenant un lien de réinitialisation de mot de passe vous a été envoyé. Veuillez vérifier votre boîte de réception.',
                onOk() {
                    //window.location.href = '/';
                }
            });
        }).catch((error) => {
            setresetPasswordError(true);
            setLoadingButton(false);
            Modal.error({
                title: 'Aucun utilisateur trouvé',
                content: 'Aucun utilisateur trouvé avec cet e-mail, veuillez réessayer avec un autre e-mail valide.',
            });
        });
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
                                </a>
                                    Réinitialiser le mot de passe
                                    <LockOutlined className={`${styles.loginIcon} ${styles.rightArrowIcon}`}/>
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
                                        className={`${styles.antdLoginInputs} ${resetPasswordError ? styles.errorInput : ''}`}
                                        validateStatus={resetPasswordError ? 'error' : ''}
                                    >
                                        <Input
                                            autoComplete="on"
                                            onChange={(e) => {
                                                userFormHandleChange(e, "email");
                                            }} placeholder='E-mail' className={`${styles.inputsLoginPage}`}
                                            prefix={<UserOutlined className={`${styles.inputsLoginPageIcons}`}/>}/>
                                    </Form.Item>







                                    <Form.Item className={`py-3 w-100`}>
                                        <Button  loading={loadingButton}
                                                 disabled={loadingButton}
                                                 onClick={() => {
                                            resetPasswordHandler();
                                        }} className={`w-100 ${styles.loginBtnSubmit}`} type="primary" htmlType="submit">
                                            Envoyer le lien de réinitialisation
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
