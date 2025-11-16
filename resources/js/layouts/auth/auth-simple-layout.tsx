import AppLogoIcon from '@/components/app-logo-icon';
import { home } from '@/routes';
import { Link } from '@inertiajs/react';
import { type PropsWithChildren } from 'react';

interface AuthLayoutProps {
    name?: string;
    title?: string;
    description?: string;
}

export default function AuthSimpleLayout({
    children,
    title,
}: PropsWithChildren<AuthLayoutProps>) {
    return (
        <div className="flex min-h-svh flex-col items-center justify-center gap-6 bg-[#F3CB51] p-6 md:p-10">
            <div className="w-full max-w-sm bg-white p-8 rounded-2xl">
                <div className="flex flex-col gap-8">
                    <div className="flex flex-col items-center gap-1">
                        <Link
                            href={home()}
                            className="flex flex-col items-center gap-1 font-medium"
                        >
                            <div className="mb-1 flex h-14 w-14 items-center justify-center rounded-md">
                                <img src="/assets/logo/5.png" alt="" />
                            </div>
                            <span className="sr-only">{title}</span>
                        </Link>

                        <div className="space-y-2 text-center">
                            <h1 className="text-2xl font-medium">{title}</h1>
                        </div>
                    </div>
                    {children}
                </div>
            </div>
        </div>
    );
}
