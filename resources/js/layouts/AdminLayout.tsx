import { ReactNode } from 'react';
import WorkspaceLayout from './WorkspaceLayout';
export default function AdminLayout({children}:{children:ReactNode}){return <WorkspaceLayout area="admin">{children}</WorkspaceLayout>}
