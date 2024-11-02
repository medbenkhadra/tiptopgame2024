import React, {useEffect, useState} from 'react';
import {Col, Row} from "react-bootstrap";
import {ArrowRightOutlined, LogoutOutlined, StarFilled} from "@ant-design/icons";
import styles from "@/styles/pages/clientStoreSelection/favoriteStoreSelectionPage.module.css";
import "../../../../app/globalsSecond.css";
import "@/styles/pages/clientStoreSelection/favoriteStoreSelectionPageGlobal.css";
import logoutService from "@/app/service/LogoutService";
import {Button, Form, Input, Modal, Pagination, Radio, Space} from 'antd';
import {associateClientToStore, getStoresForClient} from "@/app/api";

import welcomeImg from '@/assets/images/gifs/congratulations.gif';
import Image from 'next/image';
import SpinnigLoader from "@/app/components/widgets/SpinnigLoader";
import storeImg from '@/assets/images/selectStore.png';


function FavoriteStoreSelectionPage() {
    const [loading, setLoading] = useState(false);
    const {logoutAndRedirectAdminsUserToLoginPage} = logoutService();
    const [storesList, setStoresList] = useState([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [pageSize, setPageSize] = useState(9);
    const [totalStoresCount, setTotalStoresCount] = useState(0);
    const [mainLoading, setMainLoading] = useState(true);
    const [form] = Form.useForm();
    const [expand, setExpand] = useState(false);
    const [storeSearchText, setStoreSearchText] = useState("");


    //radioKey
    const [checkedStore, setCheckedStore] = useState('');

    const selectStoreOption = (e: any) => {
        console.log('radio checked', e.target.value);
        setCheckedStore(e.target.value);
    }
    const handlePageChange = (page: number) => {
        setCurrentPage(page);
    };

    function getAllStores() {
        getStoresForClient(currentPage, pageSize , storeSearchText).then((response) => {
            setStoresList(response.storesResponse);
            setTotalStoresCount(response.totalStoresCount);
            setMainLoading(false);
        }).catch((error) => {
            console.log(error);
            if (error.response) {
                if (error.response.status === 401) {
                    logoutAndRedirectAdminsUserToLoginPage();
                }
            }
        });
    }

    useEffect(() => {
        getAllStores();
    }, [currentPage , storeSearchText]);

    useEffect(() => {
        const firstLoginClientStatus = localStorage.getItem('firstLoginClientStatus');
        if (firstLoginClientStatus == "false" || firstLoginClientStatus == "" || firstLoginClientStatus == null) {
            window.location.href = '/dashboard/client';
        }
    }, []);


    function confirmStoreChoice() {
        const data = {
            storeId: checkedStore,
        };

        if (checkedStore == '' || checkedStore == null) {
            Modal.error({
                className: 'modalError',
                title: 'Aucun magasin choisi !',
                content: 'Veuillez choisir un magasin pour continuer.',
                okText: "D'accord",
            });
            return;
        } else {
            associateClientToStore(data).then((response) => {
                if (response.status == "associated") {

                    Modal.success({
                        className: 'modalSuccess',
                        title: 'Votre choix a été enregistré avec succès !',
                        content: <>
                            <div className={`${styles.modalWithGifImage}`}>
                                <Image src={welcomeImg} alt={"Bienvenu"} className={`${styles.gifImage}`}/>
                                <p>Bienvenu dans notre aventure TipTop </p>
                                <p>Vous allez être redirigé vers votre tableau de bord.</p>
                            </div>
                        </>,
                        okText: "Continuer",
                        onOk() {
                            window.location.href = '/dashboard/client';
                            localStorage.removeItem('firstLoginClientStatus');
                        }
                    })
                    setTimeout(() => {
                        window.location.href = '/dashboard/client';
                        localStorage.removeItem('firstLoginClientStatus');
                    }, 5000);
                }
            }).catch((error) => {
                if (error.response) {
                    if (error.response.status === 401) {
                        logoutAndRedirectAdminsUserToLoginPage();
                    } else {
                        Modal.error({
                            className: 'modalError',
                            title: 'Une erreur est survenue !',
                            content: 'Veuillez réessayer plus tard.',
                            okText: "D'accord",
                        });
                    }
                } else {
                    Modal.error({
                        className: 'modalError',
                        title: 'Une erreur est survenue !',
                        content: 'Veuillez réessayer plus tard.',
                        okText: "D'accord",
                    });
                }
            });
        }

    }



    const getFields = () => {
        const count = expand ? 10 : 6;
        const children = [];
        children.push(
            <Row className={`${styles.fullWidthElement} w-100 d-flex`} gutter={24}>
                <Col span={8} key={`Nomdemagasin`}>

                    <Form.Item
                        className={`${styles.formItem} searchTicketFormItem mb-5`}
                        name={`store`}
                        label={`Cherchez votre magasin préféré`}
                        initialValue=""
                    >
                        <Input className={`mt-2`}
                               placeholder="Nom du magasin"
                               onChange={(e) => {
                                   setStoreSearchText(e.target.value);
                                   setCheckedStore("");
                               }}
                        />
                    </Form.Item>

                </Col>



            </Row>,
        );

        return children;
    };

    const renderSearchForm = () => {
        return (
            <>
                <Form form={form} name="advanced_search" className={`${styles.searchTicketForm} formStoreList`}>
                    <Row className={`${styles.fullWidthElement}`} gutter={24}>{getFields()}</Row>
                    <div className={`mt-0 w-100`} style={{textAlign: 'right'}}>
                        {
                            storeSearchText != "" && (
                                <>
                                    <Space size="small">
                                        <Button
                                            className={`${styles.submitButtonBlue}`}
                                            onClick={() => {
                                                form.resetFields();
                                                setStoreSearchText("");
                                            }}
                                        >
                                            Réinitialiser
                                        </Button>
                                    </Space>
                                </>
                            )
                        }
                    </div>
                </Form>
            </>
        );

    }



    return <>
        {mainLoading && <SpinnigLoader></SpinnigLoader>}
        {!mainLoading && <>
            {!loading &&
                <div className={"favoriteStoreList"}>
                    <Row className={`${styles.storeSelectionPageTopHeader}`}>
                        <Col>
                            <Row className={`${styles.storeSelectionPageTopHeaderRow}`}>
                                <Col>
                                    <h1>
                                        Choix du magasin
                                    </h1>
                                </Col>
                            </Row>
                        </Col>

                        <Col className={`${styles.logoutDiv}`}>
                            <div onClick={logoutAndRedirectAdminsUserToLoginPage}>
                                <LogoutOutlined
                                    className={`${styles.logoutIcon}`}/>
                                <span className={`${styles.logoutSpan}`}> Se Déconnecter </span>
                            </div>
                        </Col>

                    </Row>
                    <Row className={`${styles.titlesRow}`}>
                        <Col className={`${styles.titlesCol}`}>
                            <div className={`${styles.topPageTitles}`}>
                                <h1>
                                    Merci de nous rejoindre ! C'est un honneur de vous avoir parmi nous.
                                </h1>
                                <h2>
                                    Sélectionnez votre magasin préféré pour commencer votre expérience avec nous.
                                </h2>
                            </div>
                        </Col>
                    </Row>
                    <Row className={`${styles.formDivStoreList}`}>
                        {renderSearchForm()}
                    </Row>
                    <Row className={`mt-0 pt-0`}>
                        <Col className={`${styles.antdGroupRadioBtnCol}`}>
                            <Radio.Group defaultValue={checkedStore} value={checkedStore} onChange={selectStoreOption} buttonStyle="solid">
                                {storesList.map((store: any, key: number) => <Radio.Button
                                    disabled={store.status == "2"} value={store.id} key={key}>
                                    <div className={styles.radioBtnDiv}>
                                        <Image src={storeImg} alt={"storeImg"} className={`${styles.selectStoreCardImg}`}></Image>
                                        <p><strong>Magasin : </strong>{store.name}</p>
                                        <p><strong>Adresse : </strong>{store.address}</p>
                                        <p>{store.postal_code} {store.city} {store.country}</p>
                                        <p><strong>E-mail : </strong>{store.email}</p>
                                        <p><strong>Téléphone : </strong>{store.phone}</p>
                                        {checkedStore == store.id && (
                                            <StarFilled className={`${styles.antdGroupRadioCheckedIcon}`}/>
                                        )}
                                    </div>
                                </Radio.Button>)}
                            </Radio.Group>
                        </Col>
                    </Row>
                    <Row className={`mx-5 mt-4 justify-content-between`}>
                        { totalStoresCount > 0 &&
                            (
                                <>
                                <Col>
                                    <Pagination defaultCurrent={currentPage} current={currentPage} onChange={handlePageChange}
                                                total={totalStoresCount} pageSize={pageSize}/>
                                </Col>

                        <Col className={`m-0 p-0`}>
                            <div className={`${styles.confirmStoreChoiceBtnDiv}`}>
                                <Button onClick={confirmStoreChoice} className={`${styles.confirmStoreChoiceBtn}`}
                                        type="primary" size="large">
                                    <div className={`${styles.confirmStoreChoiceBtnContent}`}>
                                        <span>Sélectionner ce magasin et continuer</span>
                                        <span>
                          <ArrowRightOutlined className={`${styles.confirmStoreChoiceBtnIcon}`}/>
                      </span>
                                    </div>
                                </Button>
                            </div>
                        </Col>
                                </>
                    )
                    }
                    </Row>

                    <Row className={`my-5`}></Row>
                </div>
            }
        </>
        }

    </>;
}

export default FavoriteStoreSelectionPage;