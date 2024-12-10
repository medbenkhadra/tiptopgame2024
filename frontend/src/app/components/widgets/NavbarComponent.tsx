"use client";

import React, { useState, useEffect } from 'react';
import Nav from 'react-bootstrap/Nav';
import Navbar from 'react-bootstrap/Navbar';
import Image from "next/image";
import logoTipTop from '@/assets/images/logof.png';
import { HomeOutlined, QuestionCircleOutlined, GiftOutlined, MailOutlined, PlayCircleOutlined, ExclamationCircleOutlined, DashboardOutlined, UserOutlined, LogoutOutlined, AppstoreFilled } from '@ant-design/icons';
import { Modal } from 'antd';

import styles from '../../../styles/components/navbar.module.css';

function NavbarComponent() {
  const [userRole, setUserRole] = useState('');
  const [user, setUser] = useState('');
  const [token, setToken] = useState('');
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const user = localStorage.getItem('loggedInUser');
    const token = localStorage.getItem('loggedInUserToken');
    const userRole = localStorage.getItem('loggedInUserRole');
    if (user) {
      setUser(JSON.parse(user));
    }
    if (token) {
      setToken(token);
    }
    if (userRole) {
      setUserRole(userRole);
    }
    setLoading(false);
  }, []);

  const logout = () => {
    Modal.confirm({
      className: 'modalError',
      title: 'Vous êtes sur le point de vous déconnecter',
      content: 'Voulez-vous continuer ?',
      okText: "Oui",
      cancelText: "Non",
      onCancel() {
        console.log('Cancel');
        Modal.destroyAll();
      },
      onOk() {
        setLoading(true);
        localStorage.removeItem('loggedInUser');
        localStorage.removeItem('loggedInUserId');
        localStorage.removeItem('loggedInUserRole');
        localStorage.removeItem('loggedInUserEmail');
        localStorage.removeItem('loggedInUserToken');
        localStorage.removeItem('selectedMenuItem');
        localStorage.removeItem('firstLoginClientStatus');
        Modal.success({
          title: 'Déconnexion réussie',
          content: 'Vous avez été déconnecté avec succès',
          okText: "D'accord",
          onOk() {
            location.reload();
          }
        });
        location.reload();
      }
    });
  }

  return (
      <div>
        <Navbar expand="xl" className={`${styles.navbar}`}>
          <div className={styles.containerNavbar}>
            <Navbar.Brand className={`${styles.navbarBrand}`} href="#home">
              <Image
                  className={`${styles.logoTipTopNavBar}`}
                  src={logoTipTop}
                  alt={"tiptop"}
              />
            </Navbar.Brand>
            <Navbar.Toggle aria-controls="basic-navbar-nav" />
            <Navbar.Collapse id="basic-navbar-nav">
              <Nav className={`${styles.linksDivNavBar}`}>
                <div className="d-flex flex-column flex-md-row">
                  <Nav.Link href="/" className={`${styles.navLink}`}><HomeOutlined className='mx-2' /> Accueil</Nav.Link>
                  <Nav.Link href="/about" className={`${styles.navLink}`}><ExclamationCircleOutlined className='mx-2' />Notre Histoires</Nav.Link>
                  <Nav.Link href="/contact" className={`${styles.navLink}`}><MailOutlined className='mx-2' />Contact</Nav.Link>
                  <Nav.Link href="/info" className={`${styles.navLink}`}><AppstoreFilled className='mx-2' /> Confidentialité</Nav.Link>
                </div>
                <div className={`${styles.rightDivNavBar}`}>
                  {!loading && (
                      <>
                        {!token && (
                            <>
                              <Nav.Link href="/store_login" className={`${styles.navLink} ${styles.navLinkStoreBtn}`}><DashboardOutlined /><span className='mx-2'>Accès Personnels</span></Nav.Link>
                              <Nav.Link href="/client_login" className={`${styles.navLink} ${styles.navLinkPlayBtn}`}><PlayCircleOutlined className='' /><span className='mx-2'>Espace client</span></Nav.Link>
                            </>
                        )}
                        {token && (
                            <>
                              {userRole === 'ROLE_CLIENT' && (
                                  <>
                                    <Nav.Link onClick={() => { localStorage.removeItem('selectedMenuItem'); }} href="/dashboard/client" className={`${styles.navLink} ${styles.navLinkStoreBtnDashboard}`}><UserOutlined /><span className='mx-2'>Espace Client</span></Nav.Link>
                                    <Nav.Link onClick={() => { logout(); }} className={`${styles.navLink} ${styles.navLinkStoreBtnSignOut}`}><LogoutOutlined /><span className='mx-2'>Se déconnecter</span></Nav.Link>
                                  </>
                              )}
                              {userRole !== 'ROLE_CLIENT' && (
                                  <>
                                    <Nav.Link onClick={() => { localStorage.removeItem('selectedMenuItem'); }} href="/dashboard/store_admin" className={`${styles.navLink} ${styles.navLinkStoreBtnDashboard}`}><DashboardOutlined /><span className='mx-2'>Tableau de board</span></Nav.Link>
                                    <Nav.Link onClick={() => { logout(); }} className={`${styles.navLink} ${styles.navLinkStoreBtnSignOut}`}><LogoutOutlined /><span className='mx-2'>Se déconnecter</span></Nav.Link>
                                  </>
                              )}
                            </>
                        )}
                      </>
                  )}
                </div>
              </Nav>
            </Navbar.Collapse>
          </div>
        </Navbar>
      </div>
  )
}

export default NavbarComponent;
