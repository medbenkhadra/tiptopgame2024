"use client";
import React from 'react';
import { Row, Col } from 'antd';
import styles from '../../../styles/components/loader.module.css';
import "../../globalsSecond.css";
import {Space, Spin } from 'antd';


const SpinnigLoader = () => {
  return (
    <div className={`${styles.loaderMain} mainSpinner `} data-testid="loading-spinner">
      <Row className={`${styles.loaderRow}`}>
        <Spin tip="" size="large">
          <div className="content" />
        </Spin>
      </Row>

    </div>
  );
};

export default SpinnigLoader;
