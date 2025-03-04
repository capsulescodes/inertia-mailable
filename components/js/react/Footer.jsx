import React from 'react';
import Table from './tags/Table';


function Footer()
{
    const name = process.env.VITE_APP_NAME;
    const date = new Date().getFullYear();

    return (

        <Table className="p-10" align="center" width="570">

            <p className="text-xs text-center text-slate-400">

                { `© ${date} ${name}. All rights reserved` }

            </p>

        </Table>
    );
};


export default Footer;
