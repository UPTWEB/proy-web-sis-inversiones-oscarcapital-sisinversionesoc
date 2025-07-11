PGDMP                      }            sistema_inversiones    13.20 (Debian 13.20-0+deb11u1)    17.4 8    &           0    0    ENCODING    ENCODING        SET client_encoding = 'UTF8';
                           false            '           0    0 
   STDSTRINGS 
   STDSTRINGS     (   SET standard_conforming_strings = 'on';
                           false            (           0    0 
   SEARCHPATH 
   SEARCHPATH     8   SELECT pg_catalog.set_config('search_path', '', false);
                           false            )           1262    16385    sistema_inversiones    DATABASE        CREATE DATABASE sistema_inversiones WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'en_US.UTF-8';
 #   DROP DATABASE sistema_inversiones;
                     ncamac    false            �            1259    16411    cliente    TABLE     �   CREATE TABLE public.cliente (
    id integer NOT NULL,
    datos_personales public.persona NOT NULL,
    estado_civil character varying(25),
    estado boolean DEFAULT true,
    usuario_id integer
);
    DROP TABLE public.cliente;
       public         heap r       ncamac    false            �            1259    16409    cliente_id_seq    SEQUENCE     �   CREATE SEQUENCE public.cliente_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 %   DROP SEQUENCE public.cliente_id_seq;
       public               ncamac    false    203            *           0    0    cliente_id_seq    SEQUENCE OWNED BY     A   ALTER SEQUENCE public.cliente_id_seq OWNED BY public.cliente.id;
          public               ncamac    false    202            �            1259    16580    intentos_login    TABLE     �   CREATE TABLE public.intentos_login (
    id integer NOT NULL,
    id_usuario integer,
    ip character varying(245),
    user_agent text,
    fecha_hora timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    exito boolean NOT NULL
);
 "   DROP TABLE public.intentos_login;
       public         heap r       ncamac    false            �            1259    16578    intentos_login_id_seq    SEQUENCE     �   CREATE SEQUENCE public.intentos_login_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 ,   DROP SEQUENCE public.intentos_login_id_seq;
       public               ncamac    false    213            +           0    0    intentos_login_id_seq    SEQUENCE OWNED BY     O   ALTER SEQUENCE public.intentos_login_id_seq OWNED BY public.intentos_login.id;
          public               ncamac    false    212            �            1259    16434 	   inversion    TABLE       CREATE TABLE public.inversion (
    id integer NOT NULL,
    cliente_id integer NOT NULL,
    plan_inversion integer NOT NULL,
    porcentaje numeric(5,2) NOT NULL,
    moneda character varying(25) NOT NULL,
    monto numeric(10,2) NOT NULL,
    meses integer NOT NULL,
    fecha_inicio date NOT NULL,
    fecha_calculada date NOT NULL,
    nombre_banco character varying(50) NOT NULL,
    cuenta_bancaria character varying(25) NOT NULL,
    cuenta_interbancaria character varying(25) NOT NULL,
    billetera_movil character varying(25),
    celular character varying(15),
    contrato text,
    estado character varying DEFAULT true,
    beneficiario1 public.persona NOT NULL,
    parentesco1 character varying NOT NULL,
    beneficiario2 public.persona,
    parentesco2 character varying
);
    DROP TABLE public.inversion;
       public         heap r       ncamac    false            �            1259    16432    inversion_id_seq    SEQUENCE     �   CREATE SEQUENCE public.inversion_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 '   DROP SEQUENCE public.inversion_id_seq;
       public               ncamac    false    205            ,           0    0    inversion_id_seq    SEQUENCE OWNED BY     E   ALTER SEQUENCE public.inversion_id_seq OWNED BY public.inversion.id;
          public               ncamac    false    204            �            1259    16451    pago    TABLE     �   CREATE TABLE public.pago (
    id integer NOT NULL,
    inversion_id integer NOT NULL,
    monto numeric(10,2) NOT NULL,
    fecha date NOT NULL,
    estado boolean DEFAULT true,
    numero_pago character varying NOT NULL,
    comprobante text
);
    DROP TABLE public.pago;
       public         heap r       ncamac    false            �            1259    16449    pago_id_seq    SEQUENCE     �   CREATE SEQUENCE public.pago_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 "   DROP SEQUENCE public.pago_id_seq;
       public               ncamac    false    207            -           0    0    pago_id_seq    SEQUENCE OWNED BY     ;   ALTER SEQUENCE public.pago_id_seq OWNED BY public.pago.id;
          public               ncamac    false    206            �            1259    16563    sesiones    TABLE     n  CREATE TABLE public.sesiones (
    id_sesion integer NOT NULL,
    id_usuario integer,
    ip character varying(45),
    user_agent text,
    inicio timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    fin timestamp without time zone,
    duracion interval(0) DEFAULT '00:00:00'::interval,
    tipo character varying(15) DEFAULT 'activo'::character varying
);
    DROP TABLE public.sesiones;
       public         heap r       ncamac    false            �            1259    16561    sesiones_id_sesion_seq    SEQUENCE     �   CREATE SEQUENCE public.sesiones_id_sesion_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 -   DROP SEQUENCE public.sesiones_id_sesion_seq;
       public               ncamac    false    211            .           0    0    sesiones_id_sesion_seq    SEQUENCE OWNED BY     Q   ALTER SEQUENCE public.sesiones_id_sesion_seq OWNED BY public.sesiones.id_sesion;
          public               ncamac    false    210            �            1259    16499    usuario    TABLE     �  CREATE TABLE public.usuario (
    id integer NOT NULL,
    username character varying(100) NOT NULL,
    password text NOT NULL,
    rol character varying(20) NOT NULL,
    estado boolean DEFAULT true,
    email character varying(35),
    CONSTRAINT usuario_rol_check CHECK (((rol)::text = ANY ((ARRAY['cliente'::character varying, 'admin'::character varying, 'superadmin'::character varying])::text[])))
);
    DROP TABLE public.usuario;
       public         heap r       ncamac    false            �            1259    16497    usuario_id_seq    SEQUENCE     �   CREATE SEQUENCE public.usuario_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 %   DROP SEQUENCE public.usuario_id_seq;
       public               ncamac    false    209            /           0    0    usuario_id_seq    SEQUENCE OWNED BY     A   ALTER SEQUENCE public.usuario_id_seq OWNED BY public.usuario.id;
          public               ncamac    false    208            o           2604    16414 
   cliente id    DEFAULT     h   ALTER TABLE ONLY public.cliente ALTER COLUMN id SET DEFAULT nextval('public.cliente_id_seq'::regclass);
 9   ALTER TABLE public.cliente ALTER COLUMN id DROP DEFAULT;
       public               ncamac    false    203    202    203            {           2604    16583    intentos_login id    DEFAULT     v   ALTER TABLE ONLY public.intentos_login ALTER COLUMN id SET DEFAULT nextval('public.intentos_login_id_seq'::regclass);
 @   ALTER TABLE public.intentos_login ALTER COLUMN id DROP DEFAULT;
       public               ncamac    false    212    213    213            q           2604    16437    inversion id    DEFAULT     l   ALTER TABLE ONLY public.inversion ALTER COLUMN id SET DEFAULT nextval('public.inversion_id_seq'::regclass);
 ;   ALTER TABLE public.inversion ALTER COLUMN id DROP DEFAULT;
       public               ncamac    false    205    204    205            s           2604    16454    pago id    DEFAULT     b   ALTER TABLE ONLY public.pago ALTER COLUMN id SET DEFAULT nextval('public.pago_id_seq'::regclass);
 6   ALTER TABLE public.pago ALTER COLUMN id DROP DEFAULT;
       public               ncamac    false    206    207    207            w           2604    16566    sesiones id_sesion    DEFAULT     x   ALTER TABLE ONLY public.sesiones ALTER COLUMN id_sesion SET DEFAULT nextval('public.sesiones_id_sesion_seq'::regclass);
 A   ALTER TABLE public.sesiones ALTER COLUMN id_sesion DROP DEFAULT;
       public               ncamac    false    210    211    211            u           2604    16502 
   usuario id    DEFAULT     h   ALTER TABLE ONLY public.usuario ALTER COLUMN id SET DEFAULT nextval('public.usuario_id_seq'::regclass);
 9   ALTER TABLE public.usuario ALTER COLUMN id DROP DEFAULT;
       public               ncamac    false    209    208    209                      0    16411    cliente 
   TABLE DATA                 public               ncamac    false    203   �C       #          0    16580    intentos_login 
   TABLE DATA                 public               ncamac    false    213   �D                 0    16434 	   inversion 
   TABLE DATA                 public               ncamac    false    205   N                 0    16451    pago 
   TABLE DATA                 public               ncamac    false    207   'P       !          0    16563    sesiones 
   TABLE DATA                 public               ncamac    false    211   {R                 0    16499    usuario 
   TABLE DATA                 public               ncamac    false    209   =\       0           0    0    cliente_id_seq    SEQUENCE SET     =   SELECT pg_catalog.setval('public.cliente_id_seq', 24, true);
          public               ncamac    false    202            1           0    0    intentos_login_id_seq    SEQUENCE SET     E   SELECT pg_catalog.setval('public.intentos_login_id_seq', 126, true);
          public               ncamac    false    212            2           0    0    inversion_id_seq    SEQUENCE SET     ?   SELECT pg_catalog.setval('public.inversion_id_seq', 30, true);
          public               ncamac    false    204            3           0    0    pago_id_seq    SEQUENCE SET     :   SELECT pg_catalog.setval('public.pago_id_seq', 95, true);
          public               ncamac    false    206            4           0    0    sesiones_id_sesion_seq    SEQUENCE SET     E   SELECT pg_catalog.setval('public.sesiones_id_sesion_seq', 73, true);
          public               ncamac    false    210            5           0    0    usuario_id_seq    SEQUENCE SET     =   SELECT pg_catalog.setval('public.usuario_id_seq', 11, true);
          public               ncamac    false    208                       2606    16421    cliente cliente_pkey 
   CONSTRAINT     R   ALTER TABLE ONLY public.cliente
    ADD CONSTRAINT cliente_pkey PRIMARY KEY (id);
 >   ALTER TABLE ONLY public.cliente DROP CONSTRAINT cliente_pkey;
       public                 ncamac    false    203            �           2606    16589 "   intentos_login intentos_login_pkey 
   CONSTRAINT     `   ALTER TABLE ONLY public.intentos_login
    ADD CONSTRAINT intentos_login_pkey PRIMARY KEY (id);
 L   ALTER TABLE ONLY public.intentos_login DROP CONSTRAINT intentos_login_pkey;
       public                 ncamac    false    213            �           2606    16443    inversion inversion_pkey 
   CONSTRAINT     V   ALTER TABLE ONLY public.inversion
    ADD CONSTRAINT inversion_pkey PRIMARY KEY (id);
 B   ALTER TABLE ONLY public.inversion DROP CONSTRAINT inversion_pkey;
       public                 ncamac    false    205            �           2606    16457    pago pago_pkey 
   CONSTRAINT     L   ALTER TABLE ONLY public.pago
    ADD CONSTRAINT pago_pkey PRIMARY KEY (id);
 8   ALTER TABLE ONLY public.pago DROP CONSTRAINT pago_pkey;
       public                 ncamac    false    207            �           2606    16572    sesiones sesiones_pkey 
   CONSTRAINT     [   ALTER TABLE ONLY public.sesiones
    ADD CONSTRAINT sesiones_pkey PRIMARY KEY (id_sesion);
 @   ALTER TABLE ONLY public.sesiones DROP CONSTRAINT sesiones_pkey;
       public                 ncamac    false    211            �           2606    16518    cliente unique_idusuario 
   CONSTRAINT     Y   ALTER TABLE ONLY public.cliente
    ADD CONSTRAINT unique_idusuario UNIQUE (usuario_id);
 B   ALTER TABLE ONLY public.cliente DROP CONSTRAINT unique_idusuario;
       public                 ncamac    false    203            �           2606    16509    usuario usuario_pkey 
   CONSTRAINT     R   ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT usuario_pkey PRIMARY KEY (id);
 >   ALTER TABLE ONLY public.usuario DROP CONSTRAINT usuario_pkey;
       public                 ncamac    false    209            �           2606    16511    usuario usuario_username_key 
   CONSTRAINT     [   ALTER TABLE ONLY public.usuario
    ADD CONSTRAINT usuario_username_key UNIQUE (username);
 F   ALTER TABLE ONLY public.usuario DROP CONSTRAINT usuario_username_key;
       public                 ncamac    false    209            �           2620    16560 $   pago trg_actualizar_estado_inversion    TRIGGER     �   CREATE TRIGGER trg_actualizar_estado_inversion AFTER UPDATE OF estado ON public.pago FOR EACH ROW WHEN (((old.estado IS DISTINCT FROM new.estado) AND (new.estado = false))) EXECUTE FUNCTION public.actualizar_estado_inversion();
 =   DROP TRIGGER trg_actualizar_estado_inversion ON public.pago;
       public               ncamac    false    207    207    207            �           2620    16558    inversion trg_generar_pagos    TRIGGER     x   CREATE TRIGGER trg_generar_pagos AFTER INSERT ON public.inversion FOR EACH ROW EXECUTE FUNCTION public.generar_pagos();
 4   DROP TRIGGER trg_generar_pagos ON public.inversion;
       public               ncamac    false    205            �           2620    16644 )   inversion trigger_actualizar_estado_pagos    TRIGGER     �   CREATE TRIGGER trigger_actualizar_estado_pagos AFTER UPDATE ON public.inversion FOR EACH ROW WHEN (((old.estado)::text IS DISTINCT FROM (new.estado)::text)) EXECUTE FUNCTION public.actualizar_estado_pagos();
 B   DROP TRIGGER trigger_actualizar_estado_pagos ON public.inversion;
       public               ncamac    false    205    205            �           2606    16512    cliente fk_cliente_usuario    FK CONSTRAINT     �   ALTER TABLE ONLY public.cliente
    ADD CONSTRAINT fk_cliente_usuario FOREIGN KEY (usuario_id) REFERENCES public.usuario(id) ON DELETE SET NULL;
 D   ALTER TABLE ONLY public.cliente DROP CONSTRAINT fk_cliente_usuario;
       public               ncamac    false    209    203    2951            �           2606    16590 -   intentos_login intentos_login_id_usuario_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.intentos_login
    ADD CONSTRAINT intentos_login_id_usuario_fkey FOREIGN KEY (id_usuario) REFERENCES public.usuario(id);
 W   ALTER TABLE ONLY public.intentos_login DROP CONSTRAINT intentos_login_id_usuario_fkey;
       public               ncamac    false    209    213    2951            �           2606    16444 #   inversion inversion_cliente_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.inversion
    ADD CONSTRAINT inversion_cliente_id_fkey FOREIGN KEY (cliente_id) REFERENCES public.cliente(id);
 M   ALTER TABLE ONLY public.inversion DROP CONSTRAINT inversion_cliente_id_fkey;
       public               ncamac    false    203    205    2943            �           2606    16458    pago pago_inversion_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.pago
    ADD CONSTRAINT pago_inversion_id_fkey FOREIGN KEY (inversion_id) REFERENCES public.inversion(id);
 E   ALTER TABLE ONLY public.pago DROP CONSTRAINT pago_inversion_id_fkey;
       public               ncamac    false    205    2947    207            �           2606    16573 !   sesiones sesiones_id_usuario_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.sesiones
    ADD CONSTRAINT sesiones_id_usuario_fkey FOREIGN KEY (id_usuario) REFERENCES public.usuario(id);
 K   ALTER TABLE ONLY public.sesiones DROP CONSTRAINT sesiones_id_usuario_fkey;
       public               ncamac    false    209    2951    211               �   x���MK�0�{Ő˶��Z�4�9D����K�m��]�v��Eq�^x���<�jx݂�Z��c6��8}�4�f�0�b��^�rfm)h��ҏ�l柉�n�a5Ko� �Pv�?N(���!I�0�Kd��W���k�ī�� �A)9���C�j�,`��G�Y�pw݄Fqr	M�����漬�q��i[wM!
�'O�қ~y�0ϳ�y�L����Z��Į�?�_(!�B6�+]�t��	p��      #   	  x�՝YoI���S�ۀ�3�x��]4\��7�@�񸑏]��~#ʘe<^�<8:-a\6������������^L�x2}8{u�~]�G�����������h��~sgZ��?;9;8^o��w�������;��;�������7�w���������O_�>��;��~�J[��?��m��><<��ӭ��Go6�>���
ܛ�&���&���>|8\��z���tO����/�x����p��j������n?�?����C6�3=?x��ӯ�[����2���H!5������du��n�b������]Ff�bԥ�q��`�z�i`d 0W�@.�Q�P� (��#n;�մ���� Vw�:60��g7ϥ��$��T���M�6&
v��+J���P\g-��� �s��  �
u&-����8�du�EZڴA�^�B� ��H
W�<
�]��Y�4��x����A(����yNB�cPK�� OW��� ��P����h���m����b����%�B`y�NY
�bE�S�Y���,�#���J�)����0c�_E���I3��LsZ��������#Q�ܙ��3Q���)�I��^�x��(x�6�ktv?�7+�S�c�W
�p��c��Y�^�(kA�C����PS�KfP�0��`�ٯt��$O}�!f��W��~F,�[kYd�2�E����� �����"敭i`�ijʛ<���	�NУ�FR������eD�K�N�R�J�:�����'B�V�`ؠ0�"5T�;��*\G�!�E�FLiEn����0��0�<��Ԇ���&�ƒ����M��DpT3ᙵ���B_f8�nNN�88zvvt��O^K�����k��C�������0�ڛ`�]�QaPxzt��y3C���[�מ�z���]F�E�s�0���A�3�s��0Z4�Ti�yf���DE���Q
�XM�=h�1��WR��(~@��jM��:F��:4��; ��NtLqJ!A<��EW37ڌ�N�ixLcP[ZV[�P�_���O4Ӟ�Ɔ�J\��"�������1��hl��܉(-��1��4��)��3���1��W��+��Ҝ6F��
mq��F�8�w��#G�!�V˃A���/hq� �nhcjT�5 `�<OqQ^7�le!7O�6 �Q/�;��yv�qyP;��i�2�Qy�(8��ƨy�v�G[T��{�o�qo:�׌�
�>���p���x�v�qo���p	?U�Y<��Ә�S����6�������ź!K�^��y�&�s��mЌ� P�4�Pa�������iA{ű�Gނ��e�j���
��E�����-��"�q�#	�!H�����.w@��*��S�C�ŅEtXU�y}4uK�����:����避���ߘ�<��xP?ɤ�U�Կ�I�mE�R�����-�Ykiz�^hÝ��,$�2k���N�E���u���e�voߔ�� ��g�A�kh�cP�����y;�����	@ޙ^�w:�t�Y1k��B׆Qv�x>��v�<q۶L������M??}9�wu�����/&�XT}b\	�����zs���n�N�6�և�KXr�-�+���0x(��l���MX�,M���+���tK:��z������*�������V<j4,��(Y��vmK���cI�+F�U�B�^��V\<a^��ܤM QT(��:mȴ�!��%�щ:�-��w;�e)Dy��]��173���e��W���;���d�����i}+e7�n��̶l��$orlU��ɱ9j��tq��79�N���N�����p�} ��j�y%�d>f�ݠu��dU4͚�c�x�um�orZNagj��ܗ�3�`Լ��a+�6�
G��ܬ�?�VHSy���ѧe��~�$1�Gؙ��K�,Fc�4fK`.o��}Q�G�."�Tcv�]�:���-����U�4�-3{73��Xt��/y'I#�J����p׺�����M����5�|5i���7�/Wy�<}��Q�\�Ŷ�	Kȕo����!6��YΣq��a<�&��p+�1�в�t�K�V��-�����K#~����xN'���jyÈ2,n�)Hؔ�T�<�/��9���h��3�{T���Aa�G_�Ћ�FyvĖYT��ƾ3;ߗ)��H��Ǽ��C�jX|�艎��Y7��h3,�#ᚗ�#��G��M�u#/5{�d��xDωk(R[���2��oL�&IKv"J,�:���B�9� E�qD������'n�@=�� CQ�)CJzp�,l߄�LY���X�         
  x��M��0���#.$��2&D=�,Ri	��h�^"^�[$��;&��UZ��z����;c{z4�Õ�\��#��L%*�e���*%�dJ����>��ͣ.���Y��LEmYɊ��L�	ܢU�U"��	�8/vq)���L'G}X�D�DPX��Dc�e#b�+^T��d���`�R��:�m,s���qga�E�?�J���X7���o}6%�F&�J	�/4QPJkm9c���t2�3�&C�5JT�6����ԑ/b/���M`d/���:ݟ��mSʈ���%s/��G�+1\oŗ���� �� ��}��`-��~|������9��A�=Mb]�z..W1oz��_�40� `wq7��:�X�<+�6��g���E���n�2��3!)-#n��s����<��OQi�QY���wV~��Y�ʴ����ւ��.��j����k$nu�[������rG���5���.2�u`�"c�m0tڨ�׈]?G�-���a��         D  x���Mo�0���
�h�4x�m����+m۽F)�]*H">���h��H���)��x<�g:~��¦󗟬=��W��-?v�ZflU����USq�i�}���j��X�ۗ˰��j��-�M�m��z_ݳ�?f�O��Θ�	�1�x�y�Ƃ���Ϗ3�����D���u6�M	�,"���P6��1�=�@��L��	&�	�"f2�'{dz/׻��Ю�r��^���'p?SW�U^y��m�1&2�KM�-&�3� Ί���D��~�}?�k"�kp2ѝ�	�&V�P�}��(j؆�h�P�T>�R�T���0���T���ʐS9�J$T����
q��r�J�ʓSJ%*��X�R)�cIK�X���nM����!�n��f�E�	xsyy�;$���m�7��!�g�C3��>�Hn��3c�u��h�ih�B��H��}ϓ�-.��E��t��2\�a�6y�O2�g8k���5Ne*CM�q*q�:bsu�=����g��"Sq��.`I]�Ry�K�-�6[��]Lꢯp�䢊x���κ�ҩ�F� ����      !   �	  x��\�nI}�+�� �qˌ,?�F�.n`fߐ���5^�l3;��߈��{�`�,���ŧ�r�Dd=}����o��/޼�>y�<W���ŧ�U�`y�n�󨳛_���\./���Gݗ��廓�������?�ai������i}���������}�{@�:����$���~������HB����tv�߫�śb�ǝ=����3���ϟ��.����>ʁR���y��Qw��}��sq���ߏ//��8J!��������S�-1�<����#�n)3Z1�XG"F��؃�Ro�-�N��듳����=�+(�Q��잜���MB��p�Y��8�ul��������ɝb
8���S���Qg��P�	B�?��ңE\#�=C3$�-�c����]�@OT5'Za*��V��� S��ԛ�H2���0EJʤ�TjO�V���,�۳?]_\}<�~�^�w�@��u�o�主��*!>P<��~����r���ϣj� �G��Wg��P,?�	c���ț�ȹZV�<��})��j�����B1�0%���<p��k���5���ǬlN�b�`�)A�X�}��ٳ���<�-A��1�Z���Vܷ^��0��G+UBA"|�z�Yu�5c�c9��֘lL�h6e��o�����ON��ܱs�	���.5`D=c
'�<i%��Ʈ�K5+�yTp��Y���²�D�܋���$ܠذ��10W0&�]o���U��e^�J.,P$>$�f�JKI�v�Y	�#.r#�3����c��91m�il�%�\��Z)>Lv��=O8�=4+�sն���[΀�@����4o%���ش�T�7.�h�'��Z��E�@~!ׯ���yKP�f�����R*��,�V���l�fZ�\c8���Z�c"a�j���P�>㺦PCǃ����߄��r��.��R
���V|A4ҡ�j�S�oI��܂�d�QB�1J�AF��#כP8�AC�>����R@֎�}l�������>��T�ʂ�Kk��x�<�cȳӊr `�i��S�}M��Cݓ<o�oR�WЉ�*��W#LZ�yIcR��]�[���0�̕p̤\ܰ���#�m(��[V�<�G�)T���L����F�/T9m��Ќ�Rl�j�*�gܹ�[�7�1$^ﮤ��+�a��|���������n��`���w<8�
�]7LQ���L:�/�K�5��`��������XWN���n'=[/�g��(5hY��\[Vt�)�����Z|��i��g^IͶ�qSk�42�� ���*�I�(��p;�IX���$>��֡&<�Z:��>�����"h�}R��X�d������8��v�	�����u�;F�����Jb��e�Z\����m�|v_����������Չ���@�[y�ֺ섎'�(Lm�T"Vzb�p�XQ'��!D��=���Ӡ O�[�	J����*�� �C�I��:Zw�`	LV*i�*.!����=�v/�SC��'-Vb��rh�MMo�W���[�Y7�^c;�Ϧ̐'��sI"���.n�_2��(��o��m��d�2�ϖ���HPX'��A.ښ��v�����8�i����O
��߮X�l��wYv*�\)��8��2���Y���7�yW?��>HIT6���m=�����ٷ�����>�6�e��9i�"m{iE* :&��
c�]	�ٰu�d�~��pؖ�3�K�{IQ����ƗP���z��Em���5#I{@�N�2�L�Þj�q�0��ܨd��gG���"����[��k6�P������zeJ�Dӕn�B�	v��jNG X�	bY+
�L_�(hИ�A�
����1E�+�͊J���3�T�e�*^��8�UQ��e;�Z����</�f�{V/Җ� D�i����m�[�gr�y(��9g���3��X���.���ݨ˟���E�Cꀪ�V?�n���ݸVUD�]%����Kw<B/�mU?�-��\��K��i��:f��h��Y 蠶�/|Av����N�x�]l���B�}����[>iÓ+�/_�|�Ŵ��	US�mV�lj�Q'���h�5V�R�g���pW-I0ʴU�p��Ll먉f�%;�v���ik2kɑ�f�'���S	IVl&��Ut�&IS,�\g�b5�eP
�������sN��o!�"x#����b��{�67�X��qG����ĒV�ꪴ=��Bk�Ɠ��'1F�}J=��O�Q"�Dц�E�j��'Yˮ�=Z�i���{����g�%E�׼��/�T+�!�s��+b��vQ�yU���h*�&�!K &�\_�K�������H��(���(4F�Ѕ�e͢���K♆m�Y�y(Ҽߐ@�F1c��^��@n5��y��R���^�v�MmlR����yr-�޽�B}��         �  x���_��8��ϧ��$g&9A��)ٛQE�Ղ�"H�"|���d�n6{��U�4y�{��o{;s��a{��J�y�ZSD�r�-�?G�NH�.����-ɰG��s����a��,�>
&+�}�?GQ���P��蝕�Wsn���eu�����ǚ��4�r�Vg�C�<w��G���ݣ�YR4�P6���Z}�������!J� ϳ@x�V��tnф�Ӑ�HR��1$wm��'AO;UU���mY�"�Y�����%�� ���
�Z�z�Z�Φ��n�w.Q�-�-6�t=��3G��d��ӖQ��'�I��p<��V��`C5��6�:���M/��IPԭ���5E��G?�/Y��A<;ܣDV�q�HCQ��k�\������F��\ԣ8���i���n"-��s���h+>c1������g�:.v�y�/.��"��0D�$��'���3�w������׺�8@��y�t'�Ey`���E��o~=��(iV�2�h�4��G��fpy�&�c�%IR �d�����;��t��(�ȉQ���IaEr�3�ڦ�K��+�>�yE9I~�N+�Dl��z)=��C�[_'���|��܃ j_E��V��X,���Bl���-��?��|�K��T��u:9Ļ��S�"�ͺ�ъֆ��Q�\�s����~��B(
�6ų�4�!$]u\p��јY���|ӕ袩W �E����⎧��p���M�     