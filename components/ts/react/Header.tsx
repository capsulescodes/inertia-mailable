import React from 'react';
import Table from './tags/Table';


interface HeaderProps
{
    logotype ? : string;
}


function Header( props : HeaderProps ) : React.JSX.Element
{
    const name = process.env.VITE_APP_NAME;
    const logotype = props.logotype || 'https://raw.githubusercontent.com/capsulescodes/inertia-mailable/main/art/capsules-inertia-mailable-logotype.png';


    return (

        <Table className="p-12 text-center">

            <img src={ logotype } className="w-12 max-h-12 mb-3" alt={ name } />

            <a href={ name } className="block text-lg font-bold no-underline text-slate-600">{ name }</a>

        </Table>

    );
};


export default Header;
