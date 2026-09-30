import { usePage } from '@inertiajs/react';
import { ReactNode } from 'react';
import WorkspaceLayout from './WorkspaceLayout';
export default function SellerLayout({children,stores,activeStoreId}:{children:ReactNode;stores?:{id:number;name:string;business_name?:string}[];activeStoreId?:number|null}){const workspace=usePage<any>().props.sellerWorkspace;return <WorkspaceLayout area="seller" stores={stores??workspace?.stores??[]} activeStoreId={activeStoreId??workspace?.activeStoreId??null}>{children}</WorkspaceLayout>}
